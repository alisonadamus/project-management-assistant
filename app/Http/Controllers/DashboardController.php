<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers;

use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Message;
use Alison\ProjectManagementAssistant\Models\Offer;
use Alison\ProjectManagementAssistant\Models\Project;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Відображення головної сторінки dashboard
     */
    public function index(): View
    {
        $user = Auth::user();

        // Отримання непрочитаних повідомлень
        $unreadMessages = $this->getUnreadMessages($user);

        // Отримання активних подій
        $activeEvents = $this->getActiveEvents($user);

        // Отримання інформації про проекти/заявки залежно від ролі
        $projectsInfo = $this->getProjectsInfo($user);

        return view('dashboard', compact('unreadMessages', 'activeEvents', 'projectsInfo'));
    }

    /**
     * Отримання непрочитаних повідомлень користувача
     */
    private function getUnreadMessages($user)
    {
        $cacheKey = "user_{$user->id}_unread_messages";
        $cacheDuration = now()->addMinutes(5); // Кешуємо на 5 хвилин

        return Cache::remember($cacheKey, $cacheDuration, function () use ($user) {
            $messages = collect();

            if ($user->hasRole('student')) {
                // Для студентів - повідомлення з проектів, де вони призначені
                $messages = Message::with(['project', 'sender'])
                    ->whereHas('project', function ($query) use ($user) {
                        $query->where('assigned_to', $user->id);
                    })
                    ->where('sender_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            } elseif ($user->hasRole('teacher')) {
                // Для викладачів - повідомлення з проектів, де вони керівники
                $messages = Message::with(['project', 'sender'])
                    ->whereHas('project.supervisor', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->where('sender_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            } elseif ($user->hasRole('admin')) {
                // Для адміністраторів - всі непрочитані повідомлення
                $messages = Message::with(['project', 'sender'])
                    ->where('is_read', false)
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            }

            return $messages;
        });
    }

    /**
     * Отримання активних подій для користувача
     */
    private function getActiveEvents($user)
    {
        $cacheKey = "user_{$user->id}_active_events";
        $cacheDuration = now()->addHour(); // Кешуємо на годину

        return Cache::remember($cacheKey, $cacheDuration, function () use ($user) {
            $query = Event::query()
                ->where('start_date', '<=', Carbon::now())
                ->where('end_date', '>=', Carbon::now())
                ->with(['category', 'projects', 'supervisors.user']);

            // Обмеження доступу до подій
            if ($user->hasRole('teacher')) {
                // Викладачі бачать тільки ті події, де вони є науковими керівниками
                $query->whereHas('supervisors', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } elseif ($user->hasRole('student')) {
                // Студенти бачать тільки ті події, де курс категорії відповідає їхньому курсу
                $query->whereHas('category', function ($q) use ($user) {
                    $q->where('course_number', $user->course_number);
                });
            }
            // Адміністратори бачать всі активні події

            return $query->get();
        });
    }

    /**
     * Отримання інформації про проекти/заявки залежно від ролі користувача
     */
    private function getProjectsInfo($user)
    {
        $cacheKey = "user_{$user->id}_projects_info";
        $cacheDuration = now()->addMinutes(15); // Кешуємо на 15 хвилин

        return Cache::remember($cacheKey, $cacheDuration, function () use ($user) {
            $info = ['type' => 'guest'];

            if ($user->hasRole('student')) {
                // Для студентів - інформація про їх проекти та заявки
                $assignedProjects = Project::with(['event', 'supervisor.user'])
                    ->where('assigned_to', $user->id)
                    ->get();

                $pendingOffers = Offer::with(['project.event', 'project.supervisor.user'])
                    ->where('student_id', $user->id)
                    ->get();

                $info = [
                    'type' => 'student',
                    'assigned_projects' => $assignedProjects,
                    'pending_offers' => $pendingOffers
                ];
            } elseif ($user->hasRole('teacher')) {
                // Для викладачів - статистика по проектах та заявках
                $supervisedProjects = Project::with(['event', 'assignedTo'])
                    ->whereHas('supervisor', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->get();

                $pendingOffers = Offer::with(['project.event', 'student'])
                    ->whereHas('project.supervisor', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->get();

                $info = [
                    'type' => 'teacher',
                    'supervised_projects' => $supervisedProjects,
                    'pending_offers' => $pendingOffers,
                    'statistics' => [
                        'total_projects' => $supervisedProjects->count(),
                        'assigned_projects' => $supervisedProjects->whereNotNull('assigned_to')->count(),
                        'unassigned_projects' => $supervisedProjects->whereNull('assigned_to')->count(),
                        'pending_offers' => $pendingOffers->count()
                    ]
                ];
            } elseif ($user->hasRole('admin')) {
                // Для адміністраторів - загальна статистика
                $totalProjects = Project::count();
                $assignedProjects = Project::whereNotNull('assigned_to')->count();
                $pendingOffers = Offer::count();
                $activeEvents = Event::where('end_date', '>=', Carbon::now())->count();

                $info = [
                    'type' => 'admin',
                    'statistics' => [
                        'total_projects' => $totalProjects,
                        'assigned_projects' => $assignedProjects,
                        'unassigned_projects' => $totalProjects - $assignedProjects,
                        'pending_offers' => $pendingOffers,
                        'active_events' => $activeEvents
                    ]
                ];
            }

            return $info;
        });
    }
}
