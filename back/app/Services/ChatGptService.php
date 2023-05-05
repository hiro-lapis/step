<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\ChatGPTApiTypeEnum;
use App\Repositories\ChatGptPrompt\ChatGptPromptRepositoryInterface;
use App\Repositories\ChatGptUsageInformation\ChatGptUsageInformationRepositoryInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Exceptions\ErrorException;

class ChatGptService
{
    public function __construct(
        private ChatGptUsageInformationRepositoryInterface $chat_gpt_usage_information_repository,
        private ChatGptPromptRepositoryInterface $chat_gpt_prompt_repository,
    )
    {}

    /**
     * chat GPT API実行し、ステップ情報の入力補完情報を取得
     * API仕様についてはconfig/openai.phpに記載
     *
     * @param array $params
     * @return array
     */
    public function completion(array $params): array
    {
        $status = Response::HTTP_OK;
        $message = '';
        $api_success = true;
        $remain_count = -1;
        // ログインユーザーの本日のチャットGPT利用情報を取得、なければ作成
        $chat_gpt_usage_information = $this->chat_gpt_usage_information_repository->firstOrCreate(auth()->user()->id, now()->format('Y-m-d'));
        if (!Gate::allows('chat-gpt-completion', $chat_gpt_usage_information)) {
            $status = Response::HTTP_FORBIDDEN;
            $message = __('messages.reached_prompt_limit');
            return compact('status', 'message', 'remain_count');
        }

        try {
            $prompt = 'タイトル：「' . $params['title'] . '」 \n' .$params['prompt'];
            // ChatGPTにリクエスト送信する
            $result = OpenAI::completions()->create([
                'model' => 'text-davinci-003',
                'prompt' => $prompt,
            ] + config('openai.params'));

            // ChatGPTからのレスポンスを取得する
            $message = trim($result['choices'][0]['text']);
        } catch (\Exception $e) {
            report($e);
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            $api_success = false;
            // 契約上限に引っかかってる場合
            if ($e instanceof ErrorException) {
                Log::error('chat GPT APIが契約上限に引っかかっています。');
                $message = __('messages.now_reached_application_prompt_limit');
            } else {
                $message = __('messages.error');
            }
        }

        // API実行結果とプロンプトをDBに保存
        try {
            DB::beginTransaction();
            $data = [
                'api_type' => ChatGPTApiTypeEnum::Completion->value,
                'success' => $api_success,
                'prompt' => $prompt,
                'response' => $api_success ? $message : null,
            ];
            $this->chat_gpt_prompt_repository->create(auth()->user(), $data);
            $this->chat_gpt_usage_information_repository->incrementUsageCount($chat_gpt_usage_information);
            $chat_gpt_usage_information->refresh();
            $remain_count = $chat_gpt_usage_information->remain_count;

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            $message = __('messages.error');
        }
        return compact('status', 'message', 'remain_count');
    }

    /**
     * ユーザーの本日のチャットGPT利用情報を取得,なければ作成し残り利用可能回数を返却
     *
     * @param integer $user_id
     * @return integer
     */
    public function getRemainCount(int $user_id): int
    {
        $chat_gpt_usage_information = $this->chat_gpt_usage_information_repository->firstOrCreate($user_id, now()->format('Y-m-d'));
        return $chat_gpt_usage_information->remain_count;
    }
}
