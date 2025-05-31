<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers;

use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class TeacherCategoryController extends Controller
{
    /**
     * Відображення форми створення нової категорії
     */
    public function create(): View
    {
        $subjects = Subject::all();
        return view('teacher.categories.create', compact('subjects'));
    }

    /**
     * Збереження нової категорії
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:32',
            'freezing_period' => 'nullable|integer|min:1',
            'course_number' => 'nullable|integer|min:1|max:4',
            'period' => 'required|integer|min:1',
            'attachments' => 'nullable|array',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
        ]);

        // Створення категорії
        $category = Category::query()->create($validated);

        // Прикріплення предметів, якщо вони вказані
        if (isset($validated['subjects'])) {
            $category->subjects()->attach($validated['subjects']);
        }

        return redirect()->route('categories.show', $category)
            ->with('success', 'Категорію успішно створено!');
    }

    /**
     * Відображення форми редагування категорії
     */
    public function edit(Category $category): View
    {
        $subjects = Subject::all();
        $selectedSubjects = $category->subjects->pluck('id')->toArray();
        
        return view('teacher.categories.edit', compact('category', 'subjects', 'selectedSubjects'));
    }

    /**
     * Оновлення категорії
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:32',
            'freezing_period' => 'nullable|integer|min:1',
            'course_number' => 'nullable|integer|min:1|max:4',
            'period' => 'required|integer|min:1',
            'attachments' => 'nullable|array',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
        ]);

        // Оновлення категорії
        $category->update($validated);

        // Оновлення прикріплених предметів
        if (isset($validated['subjects'])) {
            $category->subjects()->sync($validated['subjects']);
        } else {
            $category->subjects()->detach();
        }

        return redirect()->route('categories.show', $category)
            ->with('success', 'Категорію успішно оновлено!');
    }

    /**
     * Видалення категорії
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Перевірка, чи є пов'язані події
        if ($category->events()->count() > 0) {
            return back()->withErrors(['error' => 'Неможливо видалити категорію, оскільки з нею пов\'язані події.']);
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Категорію успішно видалено!');
    }
}
