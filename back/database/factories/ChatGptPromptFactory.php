<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ChatGPTApiTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChatGptPrompt>
 */
class ChatGptPromptFactory extends Factory
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
            'api_type' => ChatGPTApiTypeEnum::Completion->value, // 補完
            'success' => true,
            'prompt' => $this->faker->text,
            'response' => $this->faker->text,
        ];
    }
}
