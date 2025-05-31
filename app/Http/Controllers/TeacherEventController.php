<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers;

use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Event;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TeacherEventController extends Controller
{
    /**
     * Відображення форми створення нової події
     */
    public function create(): View
    {
        $categories = Category::all();
        return view('teacher.events.create', compact('categories'));
    }

    /**
     * Збереження нової події
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:128',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:512',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'bg_color' => 'nullable|string|max:7',
            'fg_color' => 'nullable|string|max:7',
            'image' => 'nullable|image|max:2048',
        ]);

        // Обробка зображення, якщо воно завантажене
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            // Зберігаємо лише відносний шлях, без повного URL
            $validated['image'] = 'storage/' . $path;
        }

        // Створення події
        $event = Event::query()->create($validated);

        // Додавання поточного викладача як керівника події
        Supervisor::query()->create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'note' => 'Організатор події',
            'slot_count' => 5, // За замовчуванням 5 місць
        ]);

        return redirect()->route('events.show', $event)
            ->with('success', 'Подію успішно створено!');
    }

    /**
     * Відображення форми редагування події
     */
    public function edit(Event $event): View
    {
        // Перевірка, чи є поточний користувач керівником цієї події
        $isSupervisor = $event->supervisors()->where('user_id', Auth::id())->exists();

        // Перевірка дозволів: або користувач є керівником події, або має дозвіл на редагування всіх подій
        if (!$isSupervisor && !Auth::user()->can('edit events')) {
            abort(403, 'Ви не маєте прав на редагування цієї події');
        }

        $categories = Category::all();
        return view('teacher.events.edit', compact('event', 'categories'));
    }

    /**
     * Оновлення події
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        \Log::info('Updating event', ['event_id' => $event->id, 'request' => $request->all()]);
        
        try {
            // Перевірка, чи є поточний користувач керівником цієї події
            $isSupervisor = $event->supervisors()->where('user_id', Auth::id())->exists();

            // Перевірка дозволів: або користувач є керівником події, або має дозвіл на редагування всіх подій
            if (!$isSupervisor && !Auth::user()->can('edit events')) {
                \Log::warning('Unauthorized attempt to update event', [
                    'user_id' => Auth::id(), 
                    'event_id' => $event->id
                ]);
                abort(403, 'Ви не маєте прав на редагування цієї події');
            }

            // Перевіряємо, чи існує категорія перед валідацією
            if ($request->has('category_id')) {
                $categoryExists = \DB::table('categories')->where('id', $request->category_id)->exists();
                if (!$categoryExists) {
                    \Log::error('Category not found', ['category_id' => $request->category_id]);
                    return back()->with('error', 'Обраної категорії не існує');
                }
            }

            $validated = $request->validate([
                'name' => 'required|string|max:128',
                'category_id' => 'required|exists:categories,id',
                'description' => 'nullable|string|max:512',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'bg_color' => 'nullable|string|max:7',
                'fg_color' => 'nullable|string|max:7',
                'image' => 'nullable|image|max:2048',
            ]);

            // Обробка зображення, якщо воно завантажене
            if ($request->hasFile('image')) {
                // Видалення старого зображення, якщо воно існує
                if ($event->image) {
                    $oldImagePath = str_replace('storage/', 'public/', $event->image);
                    if (Storage::exists($oldImagePath)) {
                        Storage::delete($oldImagePath);
                    }
                }

                $path = $request->file('image')->store('events', 'public');
                // Зберігаємо лише відносний шлях, без повного URL
                $validated['image'] = 'storage/' . $path;
            }

            // Оновлення події
            \Log::info('Updating event with data', [
                'event_id' => $event->id,
                'data' => $validated
            ]);

            // Оновлюємо дані події
            $event->update($validated);
            
            // Оновлюємо зв'язки, якщо потрібно
            $event->refresh();
            \Log::info('Event updated successfully', [
                'event_id' => $event->id,
                'category_id' => $event->category_id,
                'updated_at' => $event->updated_at
            ]);

            return redirect()->route('events.show', ['event' => $event->id])
                ->with('success', 'Подію успішно оновлено!');
                
        } catch (\Exception $e) {
            \Log::error('Error updating event', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Сталася помилка при оновленні події: ' . $e->getMessage());
        }
    }

    /**
     * Відображення сторінки управління керівниками події
     */
    public function manageSupervisors(Event $event): View
    {
        // Перевірка, чи є поточний користувач керівником цієї події
        $isSupervisor = $event->supervisors()->where('user_id', Auth::id())->exists();

        if (!$isSupervisor && !Auth::user()->hasRole('admin')) {
            abort(403, 'Ви не маєте прав на управління керівниками цієї події');
        }

        // Отримання всіх викладачів, які ще не є керівниками цієї події
        $existingSupervisorIds = $event->supervisors()->pluck('user_id')->toArray();
        $availableTeachers = User::role('teacher')
            ->whereNotIn('id', $existingSupervisorIds)
            ->get();

        return view('teacher.events.supervisors', compact('event', 'availableTeachers'));
    }

    /**
     * Додавання нового керівника до події
     */
    public function addSupervisor(Request $request, Event $event): RedirectResponse
    {
        // Перевірка, чи є поточний користувач керівником цієї події
        $isSupervisor = $event->supervisors()->where('user_id', Auth::id())->exists();

        if (!$isSupervisor && !Auth::user()->hasRole('admin')) {
            abort(403, 'Ви не маєте прав на управління керівниками цієї події');
        }

        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::notIn($event->supervisors()->pluck('user_id')->toArray())
            ],
            'note' => 'nullable|string|max:255',
            'slot_count' => 'required|integer|min:1',
        ]);

        // Перевірка, чи обраний користувач має роль "teacher"
        $user = User::query()->findOrFail($validated['user_id']);
        if (!$user->hasRole('teacher')) {
            return back()->withErrors(['user_id' => 'Обраний користувач не є викладачем']);
        }

        // Додавання нового керівника
        Supervisor::query()->create([
            'event_id' => $event->id,
            'user_id' => $validated['user_id'],
            'note' => $validated['note'],
            'slot_count' => $validated['slot_count'],
        ]);

        return redirect()->route('teacher.events.supervisors', $event)
            ->with('success', 'Керівника успішно додано!');
    }

    /**
     * Видалення керівника з події
     */
    public function removeSupervisor(Event $event, Supervisor $supervisor): RedirectResponse
    {
        // Перевірка, чи є поточний користувач керівником цієї події
        $isSupervisor = $event->supervisors()->where('user_id', Auth::id())->exists();

        if (!$isSupervisor && !Auth::user()->hasRole('admin')) {
            abort(403, 'Ви не маєте прав на управління керівниками цієї події');
        }

        // Перевірка, чи належить керівник до цієї події
        if ($supervisor->event_id !== $event->id) {
            abort(404, 'Керівник не знайдений для цієї події');
        }

        // Не дозволяємо видалити останнього керівника події
        if ($event->supervisors()->count() <= 1) {
            return back()->withErrors(['error' => 'Неможливо видалити останнього керівника події']);
        }

        // Видалення керівника
        $supervisor->delete();

        return redirect()->route('teacher.events.supervisors', $event)
            ->with('success', 'Керівника успішно видалено!');
    }

    /**
     * Оновлення інформації про керівника
     */
    public function updateSupervisor(Request $request, Event $event, Supervisor $supervisor): RedirectResponse
    {
        // Перевірка, чи є поточний користувач керівником цієї події
        $isSupervisor = $event->supervisors()->where('user_id', Auth::id())->exists();

        if (!$isSupervisor && !Auth::user()->hasRole('admin')) {
            abort(403, 'Ви не маєте прав на управління керівниками цієї події');
        }

        // Перевірка, чи належить керівник до цієї події
        if ($supervisor->event_id !== $event->id) {
            abort(404, 'Керівник не знайдений для цієї події');
        }

        $validated = $request->validate([
            'note' => 'nullable|string|max:255',
            'slot_count' => 'required|integer|min:1',
        ]);

        // Оновлення інформації про керівника
        $supervisor->update($validated);

        return redirect()->route('teacher.events.supervisors', $event)
            ->with('success', 'Інформацію про керівника успішно оновлено!');
    }

    /**
     * Видалення події
     */
    public function destroy(Event $event): RedirectResponse
    {
        \Log::info('Deleting event', ['event_id' => $event->id, 'user_id' => Auth::id()]);

        // Перевірка, чи є поточний користувач керівником цієї події
        $isSupervisor = $event->supervisors()->where('user_id', Auth::id())->exists();

        if (!$isSupervisor && !Auth::user()->hasRole('admin')) {
            \Log::warning('Unauthorized attempt to delete event', [
                'user_id' => Auth::id(),
                'event_id' => $event->id,
                'is_supervisor' => $isSupervisor,
                'is_admin' => Auth::user()->hasRole('admin')
            ]);
            abort(403, 'Ви не маєте прав на видалення цієї події');
        }

        // Видалення зображення, якщо воно існує
        if ($event->image) {
            $imagePath = str_replace('storage/', 'public/', $event->image);
            if (Storage::exists($imagePath)) {
                \Log::info('Deleting event image', ['path' => $imagePath]);
                Storage::delete($imagePath);
            } else {
                \Log::warning('Event image not found', ['path' => $imagePath]);
            }
        }

        try {
            // Видалення події (керівники видаляться автоматично завдяки cascadeOnDelete)
            $event->delete();
            \Log::info('Event deleted successfully', ['event_id' => $event->id]);

            return redirect()->route('events.index')
                ->with('success', 'Подію успішно видалено!');
                
        } catch (\Exception $e) {
            \Log::error('Error deleting event', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Сталася помилка при видаленні події: ' . $e->getMessage());
        }
    }
}
