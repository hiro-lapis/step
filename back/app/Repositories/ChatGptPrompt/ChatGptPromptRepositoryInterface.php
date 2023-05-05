<?php
declare(strict_types=1);

namespace App\Repositories\ChatGptPrompt;

use App\Models\User;

interface ChatGptPromptRepositoryInterface
{
    /**
     * データ作成
     *
     * @param User $user
     * @param array $data
     * @return void
     */
    public function create(User $user, array $data): void;
}
