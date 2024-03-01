<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(10),
            'code' => $this->faker->numerify('#### ## U## D#'),
            'lead' => $this->faker->realTextBetween($minNbChars = 150, $maxNbChars = 500),
            'is_tfe' => $this->faker->boolean()
        ];
    }
}
