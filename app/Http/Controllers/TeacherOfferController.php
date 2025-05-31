<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers;

use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Offer;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherOfferController extends Controller
{
    /**
     * Відображення списку заявок на проекти викладача
     */
    public function index(): View
    {
        $user = Auth::user();

        // Перевірка, чи має користувач дозвіл на перегляд заявок
        if (!$user->can('view offers')) {
            abort(403, 'Ви не маєте дозволу на перегляд заявок');
        }

        if ($user->hasRole('admin')) {
            // Для адміністраторів показуємо всі заявки
            $allOffers = Offer::with(['project.event', 'project.supervisor.user', 'student'])
                ->get();

            // Групуємо заявки за проектами та додаємо інформацію про проект
            $offers = $allOffers->groupBy('project_id')->map(function ($projectOffers) {
                return (object)[
                    'project' => $projectOffers->first()->project,
                    'offers' => $projectOffers
                ];
            });

            // Пагінація для проектів
            $projectIds = $allOffers->pluck('project_id')->unique();
            $currentPage = request()->get('page', 1);
            $perPage = 5;
            $paginatedProjectIds = $projectIds->forPage($currentPage, $perPage);

            // Фільтруємо заявки тільки для поточної сторінки проектів
            $offers = $offers->filter(function ($item, $key) use ($paginatedProjectIds) {
                return $paginatedProjectIds->contains($key);
            });

            // Створюємо пагінатор вручну
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $offers,
                $projectIds->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            // Отримання ID наукових керівників, де користувач є керівником
            $supervisorIds = Supervisor::where('user_id', $user->id)->pluck('id');

            // Отримання проектів, де користувач є науковим керівником
            $projects = Project::whereIn('supervisor_id', $supervisorIds)->pluck('id');

            // Отримання заявок на ці проекти
            $allOffers = Offer::with(['project.event', 'project.supervisor.user', 'student'])
                ->whereIn('project_id', $projects)
                ->get();

            // Групуємо заявки за проектами та додаємо інформацію про проект
            $offers = $allOffers->groupBy('project_id')->map(function ($projectOffers) {
                return (object)[
                    'project' => $projectOffers->first()->project,
                    'offers' => $projectOffers
                ];
            });

            // Пагінація для проектів
            $projectIds = $allOffers->pluck('project_id')->unique();
            $currentPage = request()->get('page', 1);
            $perPage = 5;
            $paginatedProjectIds = $projectIds->forPage($currentPage, $perPage);

            // Фільтруємо заявки тільки для поточної сторінки проектів
            $offers = $offers->filter(function ($item, $key) use ($paginatedProjectIds) {
                return $paginatedProjectIds->contains($key);
            });

            // Створюємо пагінатор вручну
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $offers,
                $projectIds->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        return view('teacher.offers.index', [
            'offers' => $offers,
            'paginator' => $paginator ?? null
        ]);
    }

    /**
     * Відображення заявок для конкретного проекту
     */
    public function showProjectOffers(Project $project): View
    {
        $user = Auth::user();

        // Перевірка, чи має користувач дозвіл на перегляд заявок
        if (!$user->can('view offers')) {
            abort(403, 'Ви не маєте дозволу на перегляд заявок');
        }

        // Якщо користувач не адміністратор, перевіряємо, чи є він науковим керівником цього проекту
        if (!$user->hasRole('admin')) {
            $supervisor = $project->supervisor;
            if ($supervisor->user_id != $user->id) {
                abort(403, 'Ви не є науковим керівником цього проекту');
            }
        }

        // Отримання заявок на цей проект
        $offers = Offer::with(['student'])
            ->where('project_id', $project->id)
            ->get();

        return view('teacher.offers.project', compact('project', 'offers'));
    }

    /**
     * Підтвердження заявки (призначення студента до проекту)
     */
    public function approve(Request $request, Project $project, string $studentId): RedirectResponse
    {
        $user = Auth::user();

        // Якщо користувач не адміністратор, перевіряємо, чи є він науковим керівником цього проекту
        if (!$user->hasRole('admin')) {
            $supervisor = $project->supervisor;
            if ($supervisor->user_id != $user->id) {
                abort(403, 'Ви не є науковим керівником цього проекту');
            }
        }

        // Перевірка, чи проект вже має призначеного студента
        if ($project->assigned_to !== null) {
            return back()->with('error', 'Цей проект вже має призначеного студента');
        }

        // Перевірка, чи існує заявка
        $offer = Offer::where('project_id', $project->id)
            ->where('student_id', $studentId)
            ->first();

        if (!$offer) {
            return back()->with('error', 'Заявку не знайдено');
        }

        // Перевірка, чи студент вже має проект в цій події
        $hasProjectInEvent = Project::where('event_id', $project->event_id)
            ->where('assigned_to', $studentId)
            ->exists();

        if ($hasProjectInEvent) {
            return back()->with('error', 'Цей студент вже призначений до іншого проекту в цій події');
        }

        // Перевірка, чи науковий керівник має вільні слоти
        $assignedProjectsCount = Project::where('supervisor_id', $supervisor->id)
            ->where('event_id', $project->event_id)
            ->whereNotNull('assigned_to')
            ->count();

        if ($assignedProjectsCount >= $supervisor->slot_count) {
            return back()->with('error', 'Ви вже не маєте вільних місць');
        }

        DB::beginTransaction();

        try {
            // Призначення студента до проекту
            $project->assigned_to = $studentId;
            $project->save();

            // Видалення всіх заявок студента в цій події
            $eventProjects = Project::where('event_id', $project->event_id)->pluck('id');
            Offer::whereIn('project_id', $eventProjects)
                ->where('student_id', $studentId)
                ->delete();

            // Видалення всіх заявок на цей проект
            Offer::where('project_id', $project->id)->delete();

            // Перевірка, чи досягнуто ліміту слотів
            $newAssignedProjectsCount = Project::where('supervisor_id', $supervisor->id)
                ->where('event_id', $project->event_id)
                ->whereNotNull('assigned_to')
                ->count();

            if ($newAssignedProjectsCount >= $supervisor->slot_count) {
                // Видалення всіх заявок на проекти цього керівника в цій події
                $supervisorProjects = Project::where('supervisor_id', $supervisor->id)
                    ->where('event_id', $project->event_id)
                    ->pluck('id');

                Offer::whereIn('project_id', $supervisorProjects)->delete();
            }

            DB::commit();

            return back()->with('success', 'Студента успішно призначено до проекту');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Помилка при призначенні студента: ' . $e->getMessage());
        }
    }

    /**
     * Відхилення заявки (видалення)
     */
    public function reject(Request $request, Project $project, string $studentId): RedirectResponse
    {
        $user = Auth::user();

        // Якщо користувач не адміністратор, перевіряємо, чи є він науковим керівником цього проекту
        if (!$user->hasRole('admin')) {
            $supervisor = $project->supervisor;
            if ($supervisor->user_id != $user->id) {
                abort(403, 'Ви не є науковим керівником цього проекту');
            }
        }

        // Пошук заявки
        $offer = Offer::where('project_id', $project->id)
            ->where('student_id', $studentId)
            ->first();

        if (!$offer) {
            return back()->with('error', 'Заявку не знайдено');
        }

        // Видалення заявки
        try {
            $offer->delete();
            
            // Перевіряємо, чи є ще заявки для цього проекту
            $hasMoreOffers = Offer::where('project_id', $project->id)->exists();
            
            // Якщо це була остання заявка для проекту, перенаправляємо на головну сторінку заявок
            if (!$hasMoreOffers) {
                return redirect()->route('teacher.offers.index')
                    ->with('success', 'Заявку успішно відхилено. Це була остання заявка для цього проекту.');
            }
            
            // Якщо це був запит зі сторінки проекту, повертаємося на неї
            if (str_contains(url()->previous(), '/projects/')) {
                return back()->with('success', 'Заявку успішно відхилено');
            }
            
            // Інакше оновлюємо поточну сторінку
            return back()->with('success', 'Заявку успішно відхилено');
            
        } catch (\Exception $e) {
            \Log::error('Помилка при відхиленні заявки: ' . $e->getMessage());
            return back()->with('error', 'Помилка при відхиленні заявки: ' . $e->getMessage());
        }
    }
}
