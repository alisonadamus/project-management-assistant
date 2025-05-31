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

class StudentOfferController extends Controller
{
    /**
     * Відображення списку заявок студента
     */
    public function index(): View
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            // Для адміністраторів показуємо всі заявки
            $offers = Offer::with(['project.event', 'project.supervisor.user', 'project.technologies', 'student'])
                ->paginate(12);

            return view('student.offers.index', compact('offers'));
        }

        // Перевірка, чи є користувач студентом
        if (!$user->hasRole('student')) {
            abort(403, 'Ви не маєте доступу до цієї сторінки');
        }

        // Отримання заявок студента з завантаженням зв'язаних даних
        $offers = Offer::with(['project.event', 'project.supervisor.user', 'project.technologies'])
            ->where('student_id', $user->id)
            ->paginate(12);

        return view('student.offers.index', compact('offers'));
    }

    /**
     * Створення нової заявки
     */
    public function store(Request $request, Project $project): RedirectResponse
    {
        $user = Auth::user();

        // Перевірка, чи є користувач студентом
        if (!$user->hasRole('student')) {
            abort(403, 'Ви не маєте доступу до цієї функції');
        }

        // Перевірка, чи проект вже має призначеного студента
        if ($project->assigned_to !== null) {
            return back()->with('error', 'Цей проект вже має призначеного студента');
        }

        // Перевірка, чи студент вже має проект в цій події
        $hasProjectInEvent = Project::where('event_id', $project->event_id)
            ->where('assigned_to', $user->id)
            ->exists();

        if ($hasProjectInEvent) {
            return back()->with('error', 'Ви вже призначені до проекту в цій події');
        }

        // Перевірка, чи студент вже подав заявку на цей проект
        $existingOffer = Offer::where('project_id', $project->id)
            ->where('student_id', $user->id)
            ->first();

        if ($existingOffer) {
            return back()->with('error', 'Ви вже подали заявку на цей проект');
        }

        // Перевірка, чи науковий керівник має вільні слоти
        $supervisor = $project->supervisor;
        $assignedProjectsCount = Project::where('supervisor_id', $supervisor->id)
            ->where('event_id', $project->event_id)
            ->whereNotNull('assigned_to')
            ->count();

        if ($assignedProjectsCount >= $supervisor->slot_count) {
            return back()->with('error', 'Науковий керівник вже не має вільних місць');
        }

        // Перевірка, чи відповідає курс студента курсу події
        if ($user->course_number !== $project->event->category->course_number) {
            return back()->with('error', 'Ви не можете подати заявку на проект, призначений для іншого курсу');
        }

        // Створення заявки
        try {
            Offer::create([
                'project_id' => $project->id,
                'student_id' => $user->id,
            ]);

            return back()->with('success', 'Заявку успішно подано');
        } catch (\Exception $e) {
            return back()->with('error', 'Помилка при створенні заявки: ' . $e->getMessage());
        }
    }

    /**
     * Видалення заявки
     */
    public function destroy(Project $project): RedirectResponse
    {
        $user = Auth::user();

        // Перевірка, чи є користувач студентом
        if (!$user->hasRole('student')) {
            abort(403, 'Ви не маєте доступу до цієї функції');
        }

        // Пошук заявки
        $offer = Offer::where('project_id', $project->id)
            ->where('student_id', $user->id)
            ->first();

        if (!$offer) {
            return back()->with('error', 'Заявку не знайдено');
        }

        // Видалення заявки
        try {
            $offer->delete();
            return back()->with('success', 'Заявку успішно видалено');
        } catch (\Exception $e) {
            return back()->with('error', 'Помилка при видаленні заявки: ' . $e->getMessage());
        }
    }
}
