<?php

namespace Database\Seeders;

use App\Models\Motif;
use Illuminate\Database\Seeder;

class MotifSeeder extends Seeder
{
    public function run(): void
    {
        $motifs = [
            'Chosen One',
            'Betrayal',
            'Redemption',
            'Revenge',
            'Coming of Age',
            'Good vs Evil',
            'Survival',
            'Power Corruption',
            'Identity',
            'Found Family',
            'Quest Journey',
            'Tragic Hero',
            'Time Travel',
            'Parallel Worlds',
            'Memory Loss',
            'Prophecy',
            'Sacrifice',
            'Rebellion',
            'Transformation',
        ];

        foreach ($motifs as $name) {
            Motif::query()->firstOrCreate(['name' => $name]);
        }
    }
}
