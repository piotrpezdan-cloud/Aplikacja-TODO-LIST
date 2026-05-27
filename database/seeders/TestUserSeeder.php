<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'jacek91@example.net'],
            [
                'first_name' => 'Jacek',
                'last_name' => 'Kowalski',
                'password' => bcrypt('password'),
                'role' => UserRole::USER,
                'is_active' => true,
                'locale' => 'pl',
            ]
        );
    }
}
