<?php declare(strict_types=1);

namespace App\Repositories\ChatGptUsageInformation;

use App\Models\ChatGptUsageInformation;
use Illuminate\Database\Eloquent\Collection;

class ChatGptUsageInformationRepository implements ChatGptUsageInformationRepositoryInterface
{
    public function __construct(
        private ChatGptUsageInformation $chat_gpt_usage_information,
    )
    {}

    /**
     * ユーザーの本日のチャットGPT利用情報を取得、なければ作成して取得
     *
     * @param integer $user
     * @param string $date
     * @return void
     */
    public function firstOrCreate(int $user, string $date) : ChatGptUsageInformation
    {
        return $this->chat_gpt_usage_information->firstOrCreate([
            'user_id' => $user,
            'date' => $date,
        ]);
    }

    public function incrementUsageCount(ChatGptUsageInformation $chat_gpt_usage_information): void
    {
        $chat_gpt_usage_information->increment('usage_count');
    }
}
