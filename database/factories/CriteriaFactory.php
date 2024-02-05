<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Criteria>
 */
class CriteriaFactory extends Factory
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
            'description' => $this->faker->realTextBetween($minNbChars = 100, $maxNbChars = 400),
            'indicator' => $this->faker->realTextBetween($minNbChars = 100, $maxNbChars = 400),
            'score' => $this->faker->numberBetween(0,5),
           
        ];
    }
}
