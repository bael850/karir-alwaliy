<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobPosting;
use App\Models\Applicant;
use App\Models\Application;

class DummyApplicantSeeder extends Seeder
{
    public function run(): void
    {
        $job = JobPosting::where('title', 'Backend Developer (Laravel)')->first();

        if (! $job) {
            $this->command->warn('Lowongan "backend-developer-laravel" belum ada.');
            return;
        }

        $stages = $job->stages()->orderBy('order')->get();

        $dummyData = [
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad.fauzi@example.com', 'phone' => '081234567801', 'stage' => 0, 'source' => 'LinkedIn'],
            ['name' => 'Siti Nurhaliza', 'email' => 'siti.nurhaliza@example.com', 'phone' => '081234567802', 'stage' => 1, 'source' => 'Referral'],
            ['name' => 'Budi Santoso', 'email' => 'budi.santoso@example.com', 'phone' => '081234567803', 'stage' => 2, 'source' => 'Career Page'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi.lestari@example.com', 'phone' => '081234567804', 'stage' => 0, 'source' => 'LinkedIn'],
            ['name' => 'Rizky Pratama', 'email' => 'rizky.pratama@example.com', 'phone' => '081234567805', 'stage' => 3, 'source' => 'Instagram'],
        ];

        foreach ($dummyData as $d) {
            $applicant = Applicant::firstOrCreate(
                ['email' => $d['email']],
                ['name' => $d['name'], 'phone' => $d['phone']]
            );

            Application::firstOrCreate(
                ['applicant_id' => $applicant->id, 'job_posting_id' => $job->id],
                [
                    'current_stage_id' => $stages[$d['stage']]->id,
                    'source' => $d['source'],
                    'applied_at' => now()->subDays(rand(1, 10)),
                ]
            );
        }

        $this->command->info('Selesai! ' . $job->applications()->count() . ' pelamar ditambahkan.');
    }
}