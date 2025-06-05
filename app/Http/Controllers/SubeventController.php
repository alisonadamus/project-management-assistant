<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers;

use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Subevent;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class SubeventController extends Controller
{
    /**
     * Отримання підподій для діаграми Ганта
     */
    public function getGanttData(Event $event): JsonResponse
    {
        $user = Auth::user();
        
        // Перевірка доступу до події
        if (!$this->canAccessEvent($event, $user)) {
            abort(403, 'Ви не маєте доступу до цієї події');
        }

        $subevents = $event->subevents()->orderBy('start_date')->get();
        
        $ganttData = $subevents->map(function ($subevent) {
            return [
                'id' => (string) $subevent->id,
                'name' => (string) $subevent->name,
                'description' => (string) ($subevent->description ?? ''),
                'start' => $subevent->start_date->format('Y-m-d'),
                'end' => $subevent->end_date ? $subevent->end_date->format('Y-m-d') : $subevent->start_date->addDay()->format('Y-m-d'),
                'start_datetime' => $subevent->start_date->format('Y-m-d H:i:s'),
                'end_datetime' => $subevent->end_date ? $subevent->end_date->format('Y-m-d H:i:s') : $subevent->start_date->addDay()->format('Y-m-d H:i:s'),
                'progress' => 0,
                'dependencies' => $subevent->depends_on ? [(string) $subevent->depends_on] : [],
                'custom_class' => 'subevent-' . str_replace(['#', '-'], ['', '_'], $subevent->id),
                'bg_color' => $subevent->bg_color ?? '#4F46E5',
                'fg_color' => $subevent->fg_color ?? '#FFFFFF',
            ];
        })->values(); // Переконуємося, що це масив, а не об'єкт

        return response()->json($ganttData);
    }

    /**
     * Відображення форми створення підподії
     */
    public function create(Event $event): View
    {
        $user = Auth::user();
        
        // Перевірка, чи є користувач науковим керівником цієї події
        if (!$this->isSupervisor($event, $user)) {
            abort(403, 'Тільки наукові керівники можуть створювати підподії для цієї події');
        }

        $availableSubevents = $event->subevents()->orderBy('start_date')->get();

        return view('subevents.create', compact('event', 'availableSubevents'));
    }

    /**
     * Збереження нової підподії
     */
    public function store(Request $request, Event $event): RedirectResponse
    {
        $user = Auth::user();
        
        // Перевірка, чи є користувач науковим керівником цієї події
        if (!$this->isSupervisor($event, $user)) {
            abort(403, 'Тільки наукові керівники можуть створювати підподії для цієї події');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:128',
            'description' => 'nullable|string|max:512',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'depends_on' => 'nullable|exists:subevents,id',
            'bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'fg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ], [
            'name.required' => 'Назва підподії є обов\'язковою',
            'name.max' => 'Назва підподії не може перевищувати 128 символів',
            'description.max' => 'Опис не може перевищувати 512 символів',
            'start_date.required' => 'Дата початку є обов\'язковою',
            'start_date.date' => 'Дата початку має бути коректною датою',
            'end_date.date' => 'Дата завершення має бути коректною датою',
            'end_date.after_or_equal' => 'Дата завершення не може бути раніше дати початку',
            'depends_on.exists' => 'Вибрана залежна підподія не існує',
            'bg_color.regex' => 'Колір фону має бути у форматі #RRGGBB',
            'fg_color.regex' => 'Колір тексту має бути у форматі #RRGGBB',
        ]);

        // Додаткова валідація дат з урахуванням часу
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = $validated['end_date'] ? \Carbon\Carbon::parse($validated['end_date']) : null;

        // Перевірка, що дата початку не раніше дати початку події
        if ($startDate->lt($event->start_date)) {
            return back()->withErrors([
                'start_date' => 'Дата початку підподії не може бути раніше дати початку події (' . $event->start_date->format('d.m.Y H:i') . ')'
            ])->withInput();
        }

        // Перевірка, що дата завершення не пізніше дати завершення події (якщо вона є)
        if ($event->end_date && $endDate && $endDate->gt($event->end_date)) {
            return back()->withErrors([
                'end_date' => 'Дата завершення підподії не може бути пізніше дати завершення події (' . $event->end_date->format('d.m.Y H:i') . ')'
            ])->withInput();
        }

        // Перевірка, що depends_on належить до тієї ж події
        if (!empty($validated['depends_on'])) {
            $dependentSubevent = Subevent::find($validated['depends_on']);
            if ($dependentSubevent->event_id !== $event->id) {
                return back()->withErrors(['depends_on' => 'Залежна підподія повинна належати до тієї ж події']);
            }
        }

        $subevent = new Subevent();
        $subevent->event_id = $event->id;
        $subevent->name = $validated['name'];
        $subevent->description = $validated['description'];
        $subevent->start_date = $validated['start_date'];
        $subevent->end_date = $validated['end_date'];
        $subevent->depends_on = $validated['depends_on'] ?? null;
        $subevent->bg_color = $validated['bg_color'] ?? '#4F46E5';
        $subevent->fg_color = $validated['fg_color'] ?? '#FFFFFF';
        $subevent->save();

        return redirect()->route('events.show', $event)
            ->with('success', 'Підподію успішно створено');
    }

    /**
     * Відображення форми редагування підподії
     */
    public function edit(Event $event, Subevent $subevent): View
    {
        $user = Auth::user();
        
        // Перевірка, чи належить підподія до події
        if ($subevent->event_id !== $event->id) {
            abort(404, 'Підподія не знайдена');
        }
        
        // Перевірка, чи є користувач науковим керівником цієї події
        if (!$this->isSupervisor($event, $user)) {
            abort(403, 'Тільки наукові керівники можуть редагувати підподії для цієї події');
        }

        $availableSubevents = $event->subevents()
            ->where('id', '!=', $subevent->id)
            ->orderBy('start_date')
            ->get();

        return view('subevents.edit', compact('event', 'subevent', 'availableSubevents'));
    }

    /**
     * Оновлення підподії
     */
    public function update(Request $request, Event $event, Subevent $subevent): RedirectResponse
    {
        $user = Auth::user();
        
        // Перевірка, чи належить підподія до події
        if ($subevent->event_id !== $event->id) {
            abort(404, 'Підподія не знайдена');
        }
        
        // Перевірка, чи є користувач науковим керівником цієї події
        if (!$this->isSupervisor($event, $user)) {
            abort(403, 'Тільки наукові керівники можуть редагувати підподії для цієї події');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:128',
            'description' => 'nullable|string|max:512',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'depends_on' => 'nullable|exists:subevents,id',
            'bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'fg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ], [
            'name.required' => 'Назва підподії є обов\'язковою',
            'name.max' => 'Назва підподії не може перевищувати 128 символів',
            'description.max' => 'Опис не може перевищувати 512 символів',
            'start_date.required' => 'Дата початку є обов\'язковою',
            'start_date.date' => 'Дата початку має бути коректною датою',
            'end_date.date' => 'Дата завершення має бути коректною датою',
            'end_date.after_or_equal' => 'Дата завершення не може бути раніше дати початку',
            'depends_on.exists' => 'Вибрана залежна підподія не існує',
            'bg_color.regex' => 'Колір фону має бути у форматі #RRGGBB',
            'fg_color.regex' => 'Колір тексту має бути у форматі #RRGGBB',
        ]);

        // Додаткова валідація дат з урахуванням часу
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = $validated['end_date'] ? \Carbon\Carbon::parse($validated['end_date']) : null;

        // Перевірка, що дата початку не раніше дати початку події
        if ($startDate->lt($event->start_date)) {
            return back()->withErrors([
                'start_date' => 'Дата початку підподії не може бути раніше дати початку події (' . $event->start_date->format('d.m.Y H:i') . ')'
            ])->withInput();
        }

        // Перевірка, що дата завершення не пізніше дати завершення події (якщо вона є)
        if ($event->end_date && $endDate && $endDate->gt($event->end_date)) {
            return back()->withErrors([
                'end_date' => 'Дата завершення підподії не може бути пізніше дати завершення події (' . $event->end_date->format('d.m.Y H:i') . ')'
            ])->withInput();
        }

        // Перевірка, що depends_on належить до тієї ж події та не створює циклічну залежність
        if (!empty($validated['depends_on'])) {
            $dependentSubevent = Subevent::find($validated['depends_on']);
            if ($dependentSubevent->event_id !== $event->id) {
                return back()->withErrors(['depends_on' => 'Залежна підподія повинна належати до тієї ж події']);
            }

            // Перевірка на циклічну залежність
            if ($this->wouldCreateCycle($subevent->id, $validated['depends_on'])) {
                return back()->withErrors(['depends_on' => 'Ця залежність створила б циклічну залежність']);
            }
        }

        $subevent->update($validated);

        return redirect()->route('events.show', $event)
            ->with('success', 'Підподію успішно оновлено');
    }

    /**
     * Видалення підподії
     */
    public function destroy(Event $event, Subevent $subevent): RedirectResponse
    {
        $user = Auth::user();
        
        // Перевірка, чи належить підподія до події
        if ($subevent->event_id !== $event->id) {
            abort(404, 'Підподія не знайдена');
        }
        
        // Перевірка, чи є користувач науковим керівником цієї події
        if (!$this->isSupervisor($event, $user)) {
            abort(403, 'Тільки наукові керівники можуть видаляти підподії для цієї події');
        }

        // Перевірка, чи не залежать інші підподії від цієї
        $dependentSubevents = $subevent->dependentSubevents;
        if ($dependentSubevents->isNotEmpty()) {
            return back()->withErrors(['delete' => 'Неможливо видалити підподію, оскільки від неї залежать інші підподії']);
        }

        $subevent->delete();

        return redirect()->route('events.show', $event)
            ->with('success', 'Підподію успішно видалено');
    }

    /**
     * Перевірка, чи є користувач науковим керівником події
     */
    private function isSupervisor(Event $event, $user): bool
    {
        return Supervisor::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Перевірка доступу до події
     */
    private function canAccessEvent(Event $event, $user): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        } elseif ($user->hasRole('teacher')) {
            return $this->isSupervisor($event, $user);
        } elseif ($user->hasRole('student')) {
            return $event->category->course_number == $user->course_number;
        }
        
        return false;
    }

    /**
     * Перевірка на циклічну залежність
     */
    private function wouldCreateCycle(string $subeventId, string $dependsOnId): bool
    {
        $visited = [];
        $current = $dependsOnId;
        
        while ($current && !in_array($current, $visited)) {
            if ($current === $subeventId) {
                return true;
            }
            
            $visited[] = $current;
            $subevent = Subevent::find($current);
            $current = $subevent ? $subevent->depends_on : null;
        }
        
        return false;
    }
}
