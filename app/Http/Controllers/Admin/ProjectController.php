<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::query()
            ->with('user')
            ->withCount('tasks')
            ->latest()
            ->paginate(15);

        return view('admin.projects.index', compact('projects'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,archived'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $project->update($data);

        return back()->with('success', __('messages.saved'));
    }
}
