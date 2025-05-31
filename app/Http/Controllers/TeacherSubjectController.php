<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers;

use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherSubjectController extends Controller
{
    /**
     * Відображення форми створення нового предмету
     */
    public function create(): View
    {
        $categories = Category::all();
        return view('teacher.subjects.create', compact('categories'));
    }

    /**
     * Збереження нового предмету
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:128',
            'slug' => 'required|string|max:72|unique:subjects,slug',
            'course_number' => 'required|integer|min:1|max:4',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        // Обробка зображення, якщо воно завантажене
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('subjects', 'public');
            // Зберігаємо лише відносний шлях, без повного URL
            $validated['image'] = 'storage/' . $path;
        }

        // Створення предмету
        $subject = Subject::query()->create($validated);

        // Прикріплення категорій, якщо вони вказані
        if (isset($validated['categories'])) {
            $subject->categories()->attach($validated['categories']);
        }

        return redirect()->route('subjects.show', $subject)
            ->with('success', 'Предмет успішно створено!');
    }

    /**
     * Відображення форми редагування предмету
     */
    public function edit(Subject $subject): View
    {
        $categories = Category::all();
        $selectedCategories = $subject->categories->pluck('id')->toArray();
        
        return view('teacher.subjects.edit', compact('subject', 'categories', 'selectedCategories'));
    }

    /**
     * Оновлення предмету
     */
    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:128',
            'slug' => 'required|string|max:72|unique:subjects,slug,' . $subject->id,
            'course_number' => 'required|integer|min:1|max:4',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        // Обробка зображення, якщо воно завантажене
        if ($request->hasFile('image')) {
            // Видалення старого зображення, якщо воно існує
            if ($subject->image) {
                $oldImagePath = str_replace('storage/', 'public/', $subject->image);
                if (Storage::exists($oldImagePath)) {
                    Storage::delete($oldImagePath);
                }
            }
            
            $path = $request->file('image')->store('subjects', 'public');
            // Зберігаємо лише відносний шлях, без повного URL
            $validated['image'] = 'storage/' . $path;
        }

        // Оновлення предмету
        $subject->update($validated);

        // Оновлення прикріплених категорій
        if (isset($validated['categories'])) {
            $subject->categories()->sync($validated['categories']);
        } else {
            $subject->categories()->detach();
        }

        return redirect()->route('subjects.show', $subject)
            ->with('success', 'Предмет успішно оновлено!');
    }

    /**
     * Видалення предмету
     */
    public function destroy(Subject $subject): RedirectResponse
    {
        // Видалення зображення, якщо воно існує
        if ($subject->image) {
            $imagePath = str_replace('storage/', 'public/', $subject->image);
            if (Storage::exists($imagePath)) {
                Storage::delete($imagePath);
            }
        }

        $subject->delete();

        return redirect()->route('subjects.index')
            ->with('success', 'Предмет успішно видалено!');
    }
}
