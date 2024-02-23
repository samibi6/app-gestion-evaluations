<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\CourseSection;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            AptitudeSeeder::class,
            CourseSeeder::class,
            CriteriaSeeder::class,
            SectionSeeder::class,
            StudentSeeder::class,
            IndicatorSeeder::class,
            ProficiencySeeder::class,

            CourseStudentSeeder::class,
            CriteriaStudentSeeder::class,
            SectionStudentSeeder::class,
            CourseSectionSeeder::class,
            CourseUserSeeder::class,
            ProficiencyStudentSeeder::class,
            
        ]);
    }
}
