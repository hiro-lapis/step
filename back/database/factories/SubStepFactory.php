<?php declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubStep>
 */
class SubStepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'step_id' => null,
            'name' => fake()->name(),
            'detail' => fake()->name(),
            'image_url' => fake()->imageUrl(),
            'sort_number' => fake()->unique()->numberBetween(1, 127),
        ];
    }
}
