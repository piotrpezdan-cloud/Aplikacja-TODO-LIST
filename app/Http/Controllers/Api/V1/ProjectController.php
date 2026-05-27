<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProjectStoreRequest;
use App\Http\Requests\Api\ProjectUpdateRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index(): JsonResponse
    {
        $projects = request()->user()
            ->projects()
            ->withCount('tasks')
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->orderBy('name')
            ->get();

        return response()->json(['data' => ProjectResource::collection($projects)]);
    }

    public function store(ProjectStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($request->user()->id, $data['slug'] ?? $data['name']);
        $data['status'] = $data['status'] ?? 'active';

        $project = $request->user()->projects()->create($data);

        return response()->json([
            'message' => 'Projekt został utworzony.',
            'data' => new ProjectResource($project),
        ], 201);
    }

    public function show(Project $project): JsonResponse
    {
        abort_if($project->user_id !== request()->user()->id, 403);

        return response()->json(['data' => new ProjectResource($project->loadCount('tasks'))]);
    }

    public function update(ProjectUpdateRequest $request, Project $project): JsonResponse
    {
        abort_if($project->user_id !== $request->user()->id, 403);

        $data = $request->validated();
        if (array_key_exists('slug', $data) && blank($data['slug'])) {
            unset($data['slug']);
        }
        if (isset($data['slug'])) {
            $data['slug'] = $this->uniqueSlug($request->user()->id, $data['slug'], $project->id);
        }

        $project->update($data);

        return response()->json([
            'message' => 'Projekt został zaktualizowany.',
            'data' => new ProjectResource($project->fresh()->loadCount('tasks')),
        ]);
    }

    public function destroy(Project $project): JsonResponse
    {
        abort_if($project->user_id !== request()->user()->id, 403);

        $project->delete();

        return response()->json(['message' => 'Projekt został usunięty.']);
    }

    private function uniqueSlug(int $userId, string $value, ?int $ignoreProjectId = null): string
    {
        $base = Str::slug($value) ?: 'project';
        $slug = $base;
        $counter = 2;

        while (Project::query()
            ->where('user_id', $userId)
            ->where('slug', $slug)
            ->when($ignoreProjectId, fn ($query) => $query->whereKeyNot($ignoreProjectId))
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
