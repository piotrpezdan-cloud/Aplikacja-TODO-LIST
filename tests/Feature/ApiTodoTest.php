<?php

namespace Tests\Feature;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTodoTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_login(): void
    {
        $this->postJson('/api/v1/register', [
            'first_name' => 'Jan',
            'last_name' => 'Nowak',
            'email' => 'jan@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertCreated()->assertJsonStructure(['token', 'user']);

        $this->postJson('/api/v1/login', [
            'email' => 'jan@example.com',
            'password' => 'Password123!',
        ])->assertOk()->assertJsonStructure(['token', 'user']);
    }

    public function test_categories_are_available_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        Category::factory()->create(['is_active' => true]);

        $this->actingAsApi($user)->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_user_can_crud_tasks_and_filter_them(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id, 'status' => 'active']);

        $create = $this->actingAsApi($user)->postJson('/api/v1/tasks', [
            'title' => 'Test task',
            'priority' => TaskPriority::HIGH->value,
            'status' => TaskStatus::IN_PROGRESS->value,
            'category_id' => $category->id,
            'project_id' => $project->id,
        ])->assertCreated();

        $taskId = $create->json('data.id');

        $this->actingAsApi($user)->getJson('/api/v1/tasks?status=in_progress&priority=high&project_id='.$project->id)
            ->assertOk()
            ->assertJsonPath('data.0.id', $taskId);

        $this->actingAsApi($user)->putJson('/api/v1/tasks/'.$taskId, [
            'title' => 'Updated task',
            'priority' => TaskPriority::MEDIUM->value,
            'status' => TaskStatus::DONE->value,
            'category_id' => $category->id,
            'project_id' => $project->id,
        ])->assertOk()->assertJsonPath('data.status', TaskStatus::DONE->value);

        $this->actingAsApi($user)->deleteJson('/api/v1/tasks/'.$taskId)->assertOk();
        $this->assertDatabaseMissing('tasks', ['id' => $taskId]);
    }

    public function test_user_cannot_read_or_edit_foreign_task(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $owner->id]);

        $this->actingAsApi($other)->getJson('/api/v1/tasks/'.$task->id)->assertForbidden();
        $this->actingAsApi($other)->putJson('/api/v1/tasks/'.$task->id, [
            'title' => 'Nope',
            'priority' => TaskPriority::LOW->value,
        ])->assertForbidden();
    }

    public function test_user_can_crud_projects(): void
    {
        $user = User::factory()->create();

        $create = $this->actingAsApi($user)->postJson('/api/v1/projects', [
            'name' => 'Projekt testowy',
            'status' => 'active',
        ])->assertCreated()->assertJsonPath('data.name', 'Projekt testowy');

        $projectId = $create->json('data.id');

        $this->actingAsApi($user)->getJson('/api/v1/projects')->assertOk()->assertJsonPath('data.0.id', $projectId);
        $this->actingAsApi($user)->getJson('/api/v1/projects/'.$projectId)->assertOk();
        $this->actingAsApi($user)->putJson('/api/v1/projects/'.$projectId, [
            'name' => 'Projekt po zmianie',
            'status' => 'archived',
        ])->assertOk()->assertJsonPath('data.status', 'archived');
        $this->actingAsApi($user)->deleteJson('/api/v1/projects/'.$projectId)->assertOk();
    }

    public function test_user_cannot_read_or_edit_foreign_project(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $owner->id]);

        $this->actingAsApi($other)->getJson('/api/v1/projects/'.$project->id)->assertForbidden();
        $this->actingAsApi($other)->putJson('/api/v1/projects/'.$project->id, ['name' => 'Nope'])->assertForbidden();
    }

    public function test_user_can_comment_own_task_but_not_foreign_task(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $owner->id]);

        $this->actingAsApi($owner)->postJson('/api/v1/tasks/'.$task->id.'/comments', [
            'content' => 'Komentarz testowy',
        ])->assertCreated()->assertJsonPath('data.content', 'Komentarz testowy');

        $this->actingAsApi($owner)->getJson('/api/v1/tasks/'.$task->id.'/comments')
            ->assertOk()
            ->assertJsonPath('data.0.content', 'Komentarz testowy');

        $this->actingAsApi($other)->postJson('/api/v1/tasks/'.$task->id.'/comments', [
            'content' => 'Nie moje zadanie',
        ])->assertForbidden();
    }

    public function test_comment_owner_can_update_and_delete_comment(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        $comment = TaskComment::factory()->create(['task_id' => $task->id, 'user_id' => $user->id]);

        $this->actingAsApi($user)->putJson('/api/v1/comments/'.$comment->id, [
            'content' => 'Po zmianie',
        ])->assertOk()->assertJsonPath('data.content', 'Po zmianie');

        $this->actingAsApi($user)->deleteJson('/api/v1/comments/'.$comment->id)->assertOk();
        $this->assertDatabaseMissing('task_comments', ['id' => $comment->id]);
    }

    public function test_api_endpoints_require_authorization(): void
    {
        $this->getJson('/api/v1/tasks')->assertUnauthorized();
        $this->getJson('/api/v1/projects')->assertUnauthorized();
        $this->getJson('/api/v1/categories')->assertUnauthorized();
    }

    public function test_admin_can_open_dashboard_and_regular_user_cannot(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value, 'is_active' => true]);
        $user = User::factory()->create(['role' => UserRole::USER->value, 'is_active' => true]);

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
        $this->actingAs($user)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_guest_is_redirected_from_admin_dashboard_to_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
    }

    public function test_system_admin_cannot_be_deactivated(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@todo-list.local',
            'role' => UserRole::ADMIN->value,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->patchJson('/admin/users/'.$admin->id.'/toggle-active')
            ->assertStatus(422);

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_task_due_date_cannot_be_in_the_distant_past(): void
    {
        $user = User::factory()->create();

        $this->actingAsApi($user)->postJson('/api/v1/tasks', [
            'title' => 'Old task',
            'priority' => TaskPriority::MEDIUM->value,
            'due_date' => '0001-01-01',
        ])->assertUnprocessable();
    }

    private function actingAsApi(User $user): self
    {
        Sanctum::actingAs($user);

        return $this->flushHeaders();
    }
}
