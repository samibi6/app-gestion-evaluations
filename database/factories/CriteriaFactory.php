<?php

namespace Database\Factories;

use App\Models\Aptitude;
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
            'description' => $this->faker->realTextBetween($minNbChars = 80, $maxNbChars = 200),
            'aptitude_id' => Aptitude::get()->random()->id,
        ];
    }
}
