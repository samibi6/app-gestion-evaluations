<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proficiency>
 */
class ProficiencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'criteria_skill' => $this->faker->realTextBetween($minNbChars = 80, $maxNbChars = 200),
            'indicator' => $this->faker->realTextBetween($minNbChars = 80, $maxNbChars = 200),
            'course_id' => Course::get()->random()->id,
        ];
    }
}
