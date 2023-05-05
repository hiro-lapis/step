<?php declare(strict_types=1);

namespace App\Repositories\ChatGptUsageInformation;

use App\Models\ChatGptUsageInformation;

interface ChatGptUsageInformationRepositoryInterface
{
    /**
     * ユーザーの本日のチャットGPT利用情報を取得、なければ作成して取得
     *
     * @param integer $user
     * @param string $date
     * @return ChatGptUsageInformation
     */
    public function firstOrCreate(int $user, string $date) : ChatGptUsageInformation;

    /**
     * 引数にとったチャットGPT利用情報の利用回数をインクリメント
     *
     * @param ChatGptUsageInformation $chat_gpt_usage_information
     * @return void
     */
    public function incrementUsageCount(ChatGptUsageInformation $chat_gpt_usage_information): void;
}
