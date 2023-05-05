<?php
declare(strict_types=1);

namespace App\Repositories\ChatGptPrompt;

use App\Models\ChatGptPrompt;
use App\Models\User;

class ChatGptPromptRepository implements ChatGptPromptRepositoryInterface
{
    public function __construct(
        private ChatGptPrompt $chat_gpt_prompt,
    )
    {}

    public function create(User $user, array $data): void
    {
        $user->chatGptPrompts()->create($data);
    }
}
