<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers;

use Alison\ProjectManagementAssistant\Models\Technology;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherTechnologyController extends Controller
{
    /**
     * Відображення форми створення нової технології
     */
    public function create(): View
    {
        return view('teacher.technologies.create');
    }

    /**
     * Збереження нової технології
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:128',
            'slug' => 'required|string|max:128|unique:technologies,slug',
            'description' => 'nullable|string',
            'link' => 'nullable|url|max:2048',
            'image' => 'nullable|image|max:2048',
        ]);

        // Обробка зображення, якщо воно завантажене
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('technologies', 'public');
            // Зберігаємо лише відносний шлях, без повного URL
            $validated['image'] = 'storage/' . $path;
        }

        // Створення технології
        $technology = Technology::query()->create($validated);

        return redirect()->route('technologies.show', $technology)
            ->with('success', 'Технологію успішно створено!');
    }

    /**
     * Відображення форми редагування технології
     */
    public function edit(Technology $technology): View
    {
        return view('teacher.technologies.edit', compact('technology'));
    }

    /**
     * Оновлення технології
     */
    public function update(Request $request, Technology $technology): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:128',
            'slug' => 'required|string|max:128|unique:technologies,slug,' . $technology->id,
            'description' => 'nullable|string',
            'link' => 'nullable|url|max:2048',
            'image' => 'nullable|image|max:2048',
        ]);

        // Обробка зображення, якщо воно завантажене
        if ($request->hasFile('image')) {
            // Видалення старого зображення, якщо воно існує
            if ($technology->image) {
                $oldImagePath = str_replace('storage/', 'public/', $technology->image);
                if (Storage::exists($oldImagePath)) {
                    Storage::delete($oldImagePath);
                }
            }
            
            $path = $request->file('image')->store('technologies', 'public');
            // Зберігаємо лише відносний шлях, без повного URL
            $validated['image'] = 'storage/' . $path;
        }

        // Оновлення технології
        $technology->update($validated);

        return redirect()->route('technologies.show', $technology)
            ->with('success', 'Технологію успішно оновлено!');
    }

    /**
     * Видалення технології
     */
    public function destroy(Technology $technology): RedirectResponse
    {
        // Перевірка, чи є пов'язані проекти
        if ($technology->projects()->count() > 0) {
            return back()->withErrors(['error' => 'Неможливо видалити технологію, оскільки з нею пов\'язані проекти.']);
        }

        // Видалення зображення, якщо воно існує
        if ($technology->image) {
            $imagePath = str_replace('storage/', 'public/', $technology->image);
            if (Storage::exists($imagePath)) {
                Storage::delete($imagePath);
            }
        }

        $technology->delete();

        return redirect()->route('technologies.index')
            ->with('success', 'Технологію успішно видалено!');
    }
}
