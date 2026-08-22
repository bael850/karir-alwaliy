<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Permission dasar — bisa ditambah sesuai kebutuhan nanti.
        $permissions = [
            'manage job postings',
            'manage applicants',
            'manage applications',
            'schedule interviews',
            'give interview feedback',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $hr = Role::firstOrCreate(['name' => 'HR']);
        $hr->syncPermissions([
            'manage job postings',
            'manage applicants',
            'manage applications',
            'schedule interviews',
            'give interview feedback',
        ]);

        $hrManager = Role::firstOrCreate(['name' => 'HR Manager']);
        $hrManager->syncPermissions(Permission::all()); // full access
    }
}