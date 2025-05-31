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
use Illuminate\Support\Str;

class TeacherProjectController extends Controller
{
    /**
     * Відображення форми створення нового проекту
     */
    public function create(Event $event): View
    {
        $user = Auth::user();

        // Перевірка, чи є користувач науковим керівником цієї події
        $supervisor = Supervisor::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$supervisor) {
            abort(403, 'Ви не можете створювати проекти для цієї події, оскільки не є її науковим керівником');
        }

        // Перевірка, чи є подія активною або майбутньою
        $now = now();
        if ($event->end_date && $event->end_date < $now) {
            abort(403, 'Ви не можете створювати проекти для архівної події');
        }

        $technologies = Technology::orderBy('name')->get();

        return view('teacher.projects.create', compact('event', 'supervisor', 'technologies'));
    }

    /**
     * Збереження нового проекту
     */
    public function store(Request $request, Event $event): RedirectResponse
    {
        $user = Auth::user();

        // Перевірка, чи є користувач науковим керівником цієї події
        $supervisor = Supervisor::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$supervisor) {
            abort(403, 'Ви не можете створювати проекти для цієї події, оскільки не є її науковим керівником');
        }

        // Валідація даних
        $validated = $request->validate([
            'name' => 'required|string|max:248|unique:projects,name',
            'body' => 'nullable|string',
            'appendix' => 'nullable|url|max:512',
            'technologies' => 'nullable|array',
            'technologies.*' => 'exists:technologies,id',
        ]);

        // Створення проекту
        $project = new Project();
        $project->event_id = $event->id;
        $project->supervisor_id = $supervisor->id;
        $project->name = $validated['name'];
        $project->slug = Str::slug($validated['name']);
        $project->body = $validated['body'] ?? null;
        $project->appendix = $validated['appendix'] ?? null;
        $project->save();

        // Прикріплення технологій
        if (isset($validated['technologies'])) {
            $project->technologies()->attach($validated['technologies']);
        }

        return redirect()->route('events.show', $event)
            ->with('success', 'Проект успішно створено');
    }

    /**
     * Відображення форми редагування проекту
     */
    public function edit(Project $project): View
    {
        $user = Auth::user();
        $project->load(['event', 'supervisor', 'technologies']);

        // Перевірка, чи має користувач дозвіл на редагування проектів
        if (!$user->can('edit projects')) {
            abort(403, 'Ви не маєте дозволу на редагування проектів');
        }

        // Якщо користувач не адміністратор і не є автором проекту, заборонити доступ
        if (!$user->hasRole('admin') && $project->supervisor->user_id != $user->id) {
            abort(403, 'Ви не можете редагувати цей проект, оскільки не є його автором');
        }

        $technologies = Technology::orderBy('name')->get();
        $selectedTechnologies = $project->technologies->pluck('id')->toArray();

        return view('teacher.projects.edit', compact('project', 'technologies', 'selectedTechnologies'));
    }

    /**
     * Оновлення проекту
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $user = Auth::user();

        // Перевірка, чи має користувач дозвіл на редагування проектів
        if (!$user->can('edit projects')) {
            abort(403, 'Ви не маєте дозволу на редагування проектів');
        }

        // Якщо користувач не адміністратор і не є автором проекту, заборонити доступ
        if (!$user->hasRole('admin') && $project->supervisor->user_id != $user->id) {
            abort(403, 'Ви не можете редагувати цей проект, оскільки не є його автором');
        }

        // Валідація даних
        $validated = $request->validate([
            'name' => 'required|string|max:248|unique:projects,name,' . $project->id,
            'body' => 'nullable|string',
            'appendix' => 'nullable|url|max:512',
            'technologies' => 'nullable|array',
            'technologies.*' => 'exists:technologies,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        // Якщо призначається студент, перевіряємо, чи не призначений він вже до іншого проекту цієї події
        if (isset($validated['assigned_to']) && $validated['assigned_to'] != $project->assigned_to) {
            $existingProject = Project::where('event_id', $project->event_id)
                ->where('assigned_to', $validated['assigned_to'])
                ->where('id', '!=', $project->id)
                ->first();

            if ($existingProject) {
                return back()->withErrors([
                    'assigned_to' => 'Цей студент вже призначений до іншого проекту в цій події'
                ])->withInput();
            }
        }

        // Оновлення проекту
        $project->name = $validated['name'];
        $project->slug = Str::slug($validated['name']);
        $project->body = $validated['body'] ?? null;
        $project->appendix = $validated['appendix'] ?? null;

        if (isset($validated['assigned_to'])) {
            $project->assigned_to = $validated['assigned_to'];
        }

        $project->save();

        // Оновлення технологій
        if (isset($validated['technologies'])) {
            $project->technologies()->sync($validated['technologies']);
        } else {
            $project->technologies()->detach();
        }

        return redirect()->route('events.show', $project->event_id)
            ->with('success', 'Проект успішно оновлено');
    }

    /**
     * Видалення проекту
     */
    public function destroy(Project $project): RedirectResponse
    {
        $user = Auth::user();

        // Перевірка, чи має користувач дозвіл на видалення проектів
        if (!$user->can('delete projects')) {
            abort(403, 'Ви не маєте дозволу на видалення проектів');
        }

        // Якщо користувач не адміністратор і не є автором проекту, заборонити доступ
        if (!$user->hasRole('admin') && $project->supervisor->user_id != $user->id) {
            abort(403, 'Ви не можете видалити цей проект, оскільки не є його автором');
        }

        $eventId = $project->event_id;
        $project->delete();

        return redirect()->route('events.show', $eventId)
            ->with('success', 'Проект успішно видалено');
    }
}
