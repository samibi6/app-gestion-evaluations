<?php

namespace Database\Seeders;

use App\Models\CriteriaStudent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CriteriaStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CriteriaStudent::factory(15)
        ->create();
    }
}
