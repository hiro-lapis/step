<?php

namespace Database\Factories;

use App\Enums\ChallengeStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChallengeSubStep>
 */
class ChallengeSubStepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // 基本元となるStepの情報をはめ込んで作成するため最小の設定にする
        return [
            'challenge_step_id' => null,
            'challenged_at' => now()->format('Y-m-d H:i:s'),
            'cleared_at' => null,
            'status' => ChallengeStatusEnum::Start,
            // チャレンジ時のサブステップ情報
            'sub_step_id' => null,
            'name' => null,
            'detail' => null,
            'image_url' => null,
            'sort_number' => null,
        ];
    }
}
