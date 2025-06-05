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

        // Базовий запит залежно від ролі користувача
        $query = Project::with(['event', 'supervisor.user', 'technologies', 'assignedTo', 'messages', 'offers']);

        if ($user->hasRole('admin')) {
            // Для адміністраторів показуємо всі проекти
        } elseif ($user->hasRole('student')) {
            // Для студентів показуємо проекти, до яких вони призначені
            $query->where('assigned_to', $user->id);
        } else {
            // Для викладачів показуємо проекти, де вони є науковими керівниками
            $supervisorIds = Supervisor::where('user_id', $user->id)->pluck('id');
            $query->whereIn('supervisor_id', $supervisorIds);
        }

        // Фільтрація за пошуком
        if ($request->filled('search')) {
            $query->searchByNameOrBody($request->search);
        }

        // Фільтрація за подією
        if ($request->filled('event')) {
            $query->byEvent($request->event);
        }

        // Фільтрація за технологією
        if ($request->filled('technology')) {
            $query->byTechnology($request->technology);
        }

        // Фільтрація за статусом
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Сортування
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        if ($sortBy === 'name') {
            $query->orderByName($sortDirection);
        } else {
            $query->orderByCreated($sortDirection);
        }

        $projects = $query->paginate(12);
        $projects->appends(request()->query());

        // Отримуємо дані для фільтрів
        $events = Event::orderBy('name')->get();
        $technologies = Technology::orderBy('name')->get();

        return view('projects.index', compact('projects', 'events', 'technologies'));
    }

    /**
     * Відображення списку заявок користувача
     */
    public function offers(): View
    {
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
