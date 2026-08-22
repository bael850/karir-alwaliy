<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PipelineTemplate;

class PipelineTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $default = PipelineTemplate::firstOrCreate(
            ['name' => 'Pipeline Standar'],
            ['description' => 'Alur rekrutmen umum untuk kebanyakan posisi.', 'is_default' => true]
        );

        $defaultStages = [
            ['name' => 'Screening', 'order' => 1, 'is_final' => false],
            ['name' => 'Interview HR', 'order' => 2, 'is_final' => false],
            ['name' => 'Interview User', 'order' => 3, 'is_final' => false],
            ['name' => 'Offering', 'order' => 4, 'is_final' => false],
            ['name' => 'Hired', 'order' => 5, 'is_final' => true],
            ['name' => 'Rejected', 'order' => 6, 'is_final' => true],
        ];

        foreach ($defaultStages as $stage) {
            $default->stages()->firstOrCreate(
                ['order' => $stage['order']],
                ['name' => $stage['name'], 'is_final' => $stage['is_final']]
            );
        }

        // Contoh template kedua — buat posisi yang butuh psikotes.
        $withTest = PipelineTemplate::firstOrCreate(
            ['name' => 'Pipeline dengan Psikotes'],
            ['description' => 'Untuk posisi yang butuh tahap psikotes tambahan.', 'is_default' => false]
        );

        $withTestStages = [
            ['name' => 'Screening', 'order' => 1, 'is_final' => false],
            ['name' => 'Psikotes', 'order' => 2, 'is_final' => false],
            ['name' => 'Interview HR', 'order' => 3, 'is_final' => false],
            ['name' => 'Interview User', 'order' => 4, 'is_final' => false],
            ['name' => 'Offering', 'order' => 5, 'is_final' => false],
            ['name' => 'Hired', 'order' => 6, 'is_final' => true],
            ['name' => 'Rejected', 'order' => 7, 'is_final' => true],
        ];

        foreach ($withTestStages as $stage) {
            $withTest->stages()->firstOrCreate(
                ['order' => $stage['order']],
                ['name' => $stage['name'], 'is_final' => $stage['is_final']]
            );
        }
    }
}