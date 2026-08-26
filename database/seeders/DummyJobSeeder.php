<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyJobSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::firstOrCreate(
            ['slug' => 'engineering'],
            ['name' => 'Engineering', 'description' => 'Tim pengembangan produk & teknologi']
        );

        $user = User::first() ?? User::create([
            'name' => 'Admin Sementara',
            'email' => 'admin@alwaliy-sejahtera.com',
            'password' => bcrypt('password'),
        ]);

        $jobPosting = JobPosting::firstOrCreate(
            ['slug' => 'backend-developer-laravel'],
            [
                'department_id' => $department->id,
                'created_by' => $user->id,
                'title' => 'Backend Developer Laravel',
                'location' => 'Ngawi, Jawa Timur (Hybrid)',
                'employment_type' => 'full_time',
                'description' => 'Kami mencari Backend Developer untuk membantu pengembangan platform marketplace herbal Alwaliy Sejahtera.',
                'requirements' => "- Menguasai PHP & Laravel\n- Familiar dengan MySQL\n- Mampu bekerja mandiri maupun tim",
                'status' => 'published',
                'opening_date' => now(),
                'closing_date' => now()->addMonth(),
            ]
        );

        $jobPosting->stages()->firstOrCreate(
            ['order' => 1],
            ['name' => 'Screening', 'is_final' => false]
        );
    }
}