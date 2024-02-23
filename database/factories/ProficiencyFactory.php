<?php

namespace Database\Factories;

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
            'criteria_skill' => $this->faker->realTextBetween($minNbChars = 100, $maxNbChars = 400),
            'indicator_skill' => $this->faker->realTextBetween($minNbChars = 100, $maxNbChars = 400),
        ];
    }
}
