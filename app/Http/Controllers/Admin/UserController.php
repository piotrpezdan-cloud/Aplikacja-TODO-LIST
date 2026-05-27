<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly AuditLogService $audit)
    {
    }

    public function index()
    {
        $users = User::query()->orderBy('id')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $user->update($request->validated());
        $this->audit->log($request, 'update', 'user', $user->id, 'Updated user profile');

        return back()->with('success', __('messages.saved'));
    }

    public function toggleActive(Request $request, User $user)
    {
        if ($user->email === 'admin@todo-list.local') {
            return response()->json([
                'message' => __('messages.system_admin_cannot_be_deactivated'),
            ], 422);
        }

        $user->update(['is_active' => ! $user->is_active]);
        $this->audit->log($request, 'toggle-active', 'user', $user->id, 'Toggled user active status');

        return response()->json(['is_active' => $user->is_active]);
    }
}
