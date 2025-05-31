<?php

namespace Alison\ProjectManagementAssistant\Http\Controllers;

use Alison\ProjectManagementAssistant\Models\Technology;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TechnologyController extends Controller
{
    /**
     * Відображення списку всіх технологій
     */
    public function index(Request $request): View
    {
        $query = Technology::query()->with(['projects']);

        // Фільтрація за пошуком
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Фільтрація за наявністю посилання
        if ($request->filled('has_link')) {
            if ($request->has_link == 'yes') {
                $query->withLink();
            } elseif ($request->has_link == 'no') {
                $query->withoutLink();
            }
        }

        // Фільтрація за проектом
        if ($request->filled('project')) {
            $query->byProject($request->project);
        }

        $technologies = $query->orderBy('name')->paginate(9);
        $technologies->appends(request()->query());

        return view('technologies.index', compact('technologies'));
    }

    /**
     * Відображення деталей технології
     */
    public function show(Technology $technology): View
    {
        $technology->load(['projects']);

        return view('technologies.show', compact('technology'));
    }
}
