<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers;

use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\Technology;
use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Відображення списку проектів користувача
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $page = $request->get('page', 1);
        $cacheKey = "user_{$user->id}_projects_page_{$page}";
        $cacheDuration = now()->addMinutes(30); // Кешуємо на 30 хвилин

        $projects = Cache::remember($cacheKey, $cacheDuration, function () use ($user) {
            if ($user->hasRole('admin')) {
                // Для адміністраторів показуємо всі проекти
                return Project::with(['event', 'supervisor.user', 'technologies', 'assignedTo'])
                    ->paginate(12);
            } elseif ($user->hasRole('student')) {
                // Для студентів показуємо проекти, до яких вони призначені
                return Project::with(['event', 'supervisor.user', 'technologies'])
                    ->where('assigned_to', $user->id)
                    ->paginate(12);
            } else {
                // Для викладачів показуємо проекти, де вони є науковими керівниками
                $supervisorIds = Supervisor::where('user_id', $user->id)->pluck('id');
                return Project::with(['event', 'supervisor.user', 'technologies', 'assignedTo'])
                    ->whereIn('supervisor_id', $supervisorIds)
                    ->paginate(12);
            }
        });

        return view('projects.index', compact('projects'));
    }

    /**
     * Відображення списку заявок користувача
     */
    public function offers(): View
    {
        // Цей метод буде реалізований пізніше
        return view('projects.offers');
    }

    /**
     * Відображення деталей проекту
     */
    public function show(Project $project): View
    {
        $user = Auth::user();
        $cacheKey = "project_{$project->id}_show";
        $cacheDuration = now()->addHour(); // Кешуємо на годину

        $project = Cache::remember($cacheKey, $cacheDuration, function () use ($project) {
            return $project->load([
                'event.category', 
                'supervisor.user', 
                'technologies', 
                'assignedTo', 
                'messages.sender'
            ]);
        });

        // Перевірка доступу до проекту
        if ($user->hasRole('admin')) {
            // Адміністратор має доступ до всіх проектів
        } elseif ($user->hasRole('teacher')) {
            // Викладач повинен бути керівником проекту
            if ($project->supervisor && $project->supervisor->user_id != $user->id) {
                abort(403, 'Ви не маєте доступу до цього проекту, оскільки не є його науковим керівником');
            }
        } elseif ($user->hasRole('student')) {
            // Студент повинен бути призначений до проекту або проект має бути доступний для його курсу
            if ($project->assigned_to != $user->id && $project->event->category->course_number != $user->course_number) {
                abort(403, 'Ви не маєте доступу до цього проекту');
            }
        }

        return view('projects.show', compact('project'));
    }
}
