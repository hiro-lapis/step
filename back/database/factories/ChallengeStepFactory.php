<?php

namespace Database\Factories;

use App\Enums\ChallengeStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChallengeStep>
 */
class ChallengeStepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'challenge_user_id' => null,
            'challenged_at' => now()->format('Y-m-d H:i:s'),
            'cleared_at' => null,
            'status' => ChallengeStatusEnum::Challenging,
            // チャレンジ時のステップ情報
            'step_id' => null,
            'post_user_id' => null,
            'category_id' => null,
            'achievement_time_type_id' => null,
            'name' => fake()->text(),
            'image_url' => fake()->imageUrl(),
            'summary' => fake()->text(),
            'merit' => fake()->text(),
        ];
    }
}
