<?php

namespace Database\Seeders;

use App\Models\ProficiencyStudent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProficiencyStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProficiencyStudent::factory(15)
        ->create();
    }
}
