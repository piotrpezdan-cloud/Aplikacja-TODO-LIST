<?php

namespace Database\Seeders;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'jacek91@example.net')->firstOrFail();
        $categories = Category::pluck('id', 'name');

        $projects = collect([
            ['name' => 'Projekt uczelniany', 'description' => 'Zadania związane z oddaniem projektu bazodanowego.', 'due_date' => now()->addWeeks(2)->toDateString()],
            ['name' => 'Organizacja domu', 'description' => 'Codzienne obowiązki i zakupy.', 'due_date' => now()->addMonth()->toDateString()],
            ['name' => 'Plan zdrowia', 'description' => 'Nawyki, treningi i wizyty.', 'due_date' => null],
        ])->mapWithKeys(function (array $data) use ($user) {
            $slug = Str::slug($data['name']);

            $project = Project::updateOrCreate(
                ['user_id' => $user->id, 'slug' => $slug],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'status' => 'active',
                    'due_date' => $data['due_date'],
                ]
            );

            return [$data['name'] => $project];
        });

        $tasks = [
            ['title' => 'Dokończyć ERD', 'status' => TaskStatus::IN_PROGRESS, 'priority' => TaskPriority::HIGH, 'category' => 'Studia', 'project' => 'Projekt uczelniany', 'due' => '+2 days'],
            ['title' => 'Opisać migracje w dokumentacji', 'status' => TaskStatus::NEW, 'priority' => TaskPriority::HIGH, 'category' => 'Studia', 'project' => 'Projekt uczelniany', 'due' => '+3 days'],
            ['title' => 'Przetestować CRUD zadań', 'status' => TaskStatus::DONE, 'priority' => TaskPriority::MEDIUM, 'category' => 'Praca', 'project' => 'Projekt uczelniany', 'due' => '-1 day'],
            ['title' => 'Zrobić zakupy tygodniowe', 'status' => TaskStatus::NEW, 'priority' => TaskPriority::MEDIUM, 'category' => 'Zakupy', 'project' => 'Organizacja domu', 'due' => '+1 day'],
            ['title' => 'Posprzątać biurko', 'status' => TaskStatus::NEW, 'priority' => TaskPriority::LOW, 'category' => 'Dom', 'project' => 'Organizacja domu', 'due' => '+5 days'],
            ['title' => 'Trening cardio', 'status' => TaskStatus::IN_PROGRESS, 'priority' => TaskPriority::MEDIUM, 'category' => 'Zdrowie', 'project' => 'Plan zdrowia', 'due' => '+4 days'],
            ['title' => 'Archiwalne notatki', 'status' => TaskStatus::ARCHIVED, 'priority' => TaskPriority::LOW, 'category' => 'Studia', 'project' => 'Projekt uczelniany', 'due' => '-7 days'],
            ['title' => 'Wysłać mail do prowadzącego', 'status' => TaskStatus::DONE, 'priority' => TaskPriority::HIGH, 'category' => 'Studia', 'project' => 'Projekt uczelniany', 'due' => 'today'],
            ['title' => 'Przygotować listę pytań', 'status' => TaskStatus::NEW, 'priority' => TaskPriority::MEDIUM, 'category' => 'Praca', 'project' => null, 'due' => '+8 days'],
            ['title' => 'Odebrać paczkę', 'status' => TaskStatus::NEW, 'priority' => TaskPriority::LOW, 'category' => 'Zakupy', 'project' => null, 'due' => '+6 days'],
            ['title' => 'Wizyta kontrolna', 'status' => TaskStatus::NEW, 'priority' => TaskPriority::HIGH, 'category' => 'Zdrowie', 'project' => 'Plan zdrowia', 'due' => '+10 days'],
            ['title' => 'Aktualizacja README', 'status' => TaskStatus::IN_PROGRESS, 'priority' => TaskPriority::HIGH, 'category' => 'Studia', 'project' => 'Projekt uczelniany', 'due' => '+2 days'],
        ];

        foreach ($tasks as $item) {
            $task = Task::updateOrCreate(
                ['user_id' => $user->id, 'title' => $item['title']],
                [
                    'category_id' => $categories[$item['category']] ?? null,
                    'project_id' => $item['project'] ? $projects[$item['project']]->id : null,
                    'description' => 'Dane demonstracyjne do testowania systemu ToDo List.',
                    'status' => $item['status'],
                    'priority' => $item['priority'],
                    'due_date' => now()->modify($item['due'])->toDateString(),
                    'completed_at' => $item['status'] === TaskStatus::DONE ? now() : null,
                ]
            );

            TaskComment::firstOrCreate(
                ['task_id' => $task->id, 'user_id' => $user->id, 'content' => 'Komentarz testowy dla: '.$task->title]
            );
        }

        AuditLog::firstOrCreate(
            ['action' => 'seed', 'entity_type' => 'database', 'entity_id' => null],
            [
                'user_id' => $user->id,
                'description' => 'Utworzono przykładowe dane dla projektu uczelnianego.',
                'ip_address' => '127.0.0.1',
            ]
        );
    }
}
