<?php declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Step>
 */
class StepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => null,
            'category_id' => null,
            'name' => fake()->name(),
            'achievement_date_count' => fake()->numberBetween(1, 100),
            'achievement_date_time' => fake()->time(),
        ];
    }
}
