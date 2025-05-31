<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; 

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ]);

        User::factory()->create([
            'name' => 'Supervisor User',
            'email' => 'supervisor@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUPERVISOR,
            'email_verified_at' => now(),
        ]);

        User::factory()->create([
            'name' => 'Employee One',
            'email' => 'employee1@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_EMPLOYEE,
            'email_verified_at' => now(),
        ]);

        User::factory()->create([
            'name' => 'Employee Two',
            'email' => 'employee2@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_EMPLOYEE,
            'email_verified_at' => now(),
        ]);
    }
}