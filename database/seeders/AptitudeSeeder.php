<?php

namespace Database\Seeders;

use App\Models\Aptitude;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AptitudeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Aptitude::factory(15)
        ->create();
    }
}
