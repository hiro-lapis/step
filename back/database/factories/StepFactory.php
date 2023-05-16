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
            'name' => fake()->name(),
            'user_id' => null,
            'category_id' => null,
            'image_url' => fake()->optional()->imageUrl(),
            'achievement_time_type_id' => null,
            'summary' => fake()->text(),
        ];
    }
}
