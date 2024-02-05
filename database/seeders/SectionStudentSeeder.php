<?php

namespace Database\Seeders;

use App\Models\SectionStudent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SectionStudent::factory(10)
        ->create();
    }
}
