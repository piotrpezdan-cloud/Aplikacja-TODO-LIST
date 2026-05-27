<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CommentStoreRequest;
use App\Http\Requests\Api\CommentUpdateRequest;
use App\Http\Resources\TaskCommentResource;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\JsonResponse;

class TaskCommentController extends Controller
{
    public function index(Task $task): JsonResponse
    {
        abort_if($task->user_id !== request()->user()->id, 403);

        $comments = $task->comments()->with('user')->oldest()->get();

        return response()->json(['data' => TaskCommentResource::collection($comments)]);
    }

    public function store(CommentStoreRequest $request, Task $task): JsonResponse
    {
        abort_if($task->user_id !== $request->user()->id, 403);

        $comment = $task->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->validated('content'),
        ]);

        return response()->json([
            'message' => 'Komentarz został dodany.',
            'data' => new TaskCommentResource($comment->load('user')),
        ], 201);
    }

    public function update(CommentUpdateRequest $request, TaskComment $comment): JsonResponse
    {
        abort_if($comment->user_id !== $request->user()->id, 403);

        $comment->update($request->validated());

        return response()->json([
            'message' => 'Komentarz został zaktualizowany.',
            'data' => new TaskCommentResource($comment->fresh('user')),
        ]);
    }

    public function destroy(TaskComment $comment): JsonResponse
    {
        abort_if($comment->user_id !== request()->user()->id, 403);

        $comment->delete();

        return response()->json(['message' => 'Komentarz został usunięty.']);
    }
}
