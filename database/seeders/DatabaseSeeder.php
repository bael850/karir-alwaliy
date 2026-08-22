<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DepartmentSeeder::class,
            PipelineTemplateSeeder::class,
        ]);

        // Akun HR pertama buat login & testing — GANTI password ini setelah seeding.
        $admin = User::firstOrCreate(
            ['email' => 'admin@alwaliy-sejahtera.com'],
            ['name' => 'Admin HR', 'password' => bcrypt('password')]
        );

        $admin->assignRole('HR Manager');
    }
}