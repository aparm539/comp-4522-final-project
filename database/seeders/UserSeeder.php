<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create the admin user
        $adminUser = User::create([
            'name' => 'Test Admin',
            'email' => 'test@test.com',
            'password' => Hash::make('test'),
        ]);

        $viewUser = User::create([
            'name' => 'Test Viewer',
            'email' => 'test1@test.com',
            'password' => Hash::make('test'),
        ]);

        // Assign admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminUser->roles()->attach($adminRole->id);
        }
        $viewRole = Role::where('name', 'viewer')->first();
        if ($viewRole) {
            $viewUser->roles()->attach($viewRole->id);
        }
    }
}
