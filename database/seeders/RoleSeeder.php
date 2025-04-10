<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'viewer',
                'description' => 'Users who can only view data'
            ],
            [
                'name' => 'researcher',
                'description' => 'Users who can view and update chemicals and containers'
            ],
            [
                'name' => 'admin',
                'description' => 'Users with full administrative permissions'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
} 