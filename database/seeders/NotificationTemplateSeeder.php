<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        NotificationTemplate::updateOrCreate(
            ['key' => 'application_received'],
            [
                'subject' => 'Lamaran Kamu Sudah Kami Terima - {{job_title}}',
                'body' => "Halo {{applicant_name}},\n\nTerima kasih sudah melamar posisi {{job_title}} di Alwaliy Sejahtera. Lamaranmu sudah kami terima dan akan segera ditinjau oleh tim rekrutmen kami.\n\nTerima kasih,\nTim Rekrutmen Alwaliy Sejahtera",
            ]
        );

        NotificationTemplate::updateOrCreate(
            ['key' => 'application_stage_changed'],
            [
                'subject' => 'Update Lamaran Kamu - {{job_title}}',
                'body' => "Halo {{applicant_name}},\n\nStatus lamaran kamu untuk posisi {{job_title}} sekarang sudah masuk tahap: {{stage_name}}.\n\nTerima kasih,\nTim Rekrutmen Alwaliy Sejahtera",
            ]
        );

        NotificationTemplate::updateOrCreate(
            ['key' => 'interview_scheduled'],
            [
                'subject' => 'Jadwal Interview - {{job_title}}',
                'body' => "Halo {{applicant_name}},\n\nKamu dijadwalkan interview untuk posisi {{job_title}} pada tahap {{stage_name}}.\n\nWaktu: {{scheduled_at}}\nLokasi/Link: {{location_or_link}}\n\nSampai jumpa,\nTim Rekrutmen Alwaliy Sejahtera",
            ]
        );
    }
}