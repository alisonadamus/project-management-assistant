<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers;

use Alison\ProjectManagementAssistant\Models\Category;
use Alison\ProjectManagementAssistant\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
    /**
     * Відображення списку всіх категорій
     */
    public function index(Request $request): View
    {
        $query = Category::query()->with(['subjects']);

        // Фільтрація за пошуком
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Фільтрація за курсом
        if ($request->filled('course_number')) {
            $query->where('course_number', $request->course_number);
        }

        // Фільтрація за предметом
        if ($request->filled('subject')) {
            $query->withSubject($request->subject);
        }

        // Фільтрація за періодом
        if ($request->filled('min_period')) {
            $query->minPeriod($request->min_period);
        }

        if ($request->filled('max_period')) {
            $query->maxPeriod($request->max_period);
        }

        $categories = $query->orderBy('name')->paginate(9);
        $categories->appends(request()->query());

        $subjects = Subject::all();

        return view('categories.index', compact('categories', 'subjects'));
    }

    /**
     * Відображення деталей категорії
     */
    public function show(Category $category): View
    {
        $category->load(['subjects']);

        return view('categories.show', compact('category'));
    }
}
