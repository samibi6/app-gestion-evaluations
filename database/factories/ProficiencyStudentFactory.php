<?php

namespace Database\Factories;

use App\Models\Proficiency;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProficiencyStudent>
 */
class ProficiencyStudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proficiency_id' => Proficiency::get()->random()->id,
            'student_id' => Student::get()->random()->id,
            'acquired_skill' => $this->faker->boolean(),
            'score' => $this->faker->numberBetween(0,10),
        ];
    }
}
