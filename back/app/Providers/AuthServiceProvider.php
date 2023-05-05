<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Enums\ChallengeStatusEnum;
use App\Models\ChallengeStep;
use App\Models\ChallengeSubStep;
use App\Models\ChatGptUsageInformation;
use App\Models\Step;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // チャレンジ新規作成
        Gate::define('store-challenge', function (User $user, Step $step) {
            // 自身が投稿したステップでないか
            if ($user->id === $step->user_id) return false;
            // 現在挑戦中でないか
            $in_challenging = ChallengeStep::stepId($step->id)->challengeUserId($user->id)->challenging()->exists();
            if ($in_challenging) return false;
            return true;
        });
        // チャレンジ更新
        Gate::define('update-challenge-sub-step', function (User $user, ChallengeStep $challenge_step, ChallengeSubStep $challenge_sub_step) {
            // 自身が投稿したチャレンジか
            if ($user->id !== $challenge_step->user_id) return false;
            // 対象のサブステップが現在挑戦中か
            if (!ChallengeStatusEnum::isInChallenge($challenge_sub_step->status)) return false;
            return true;
        });
        // ステップ更新
        Gate::define('edit-step', function (User $user, Step $step) {
            // 自身が投稿したステップか
            if ($user->id !== $step->user_id) return false;
            return true;
        });
        // ステップ削除
        Gate::define('delete-step', function (User $user, Step $step) {
            // 自身が投稿したステップか
            if ($user->id !== $step->user_id) return false;
            return true;
        });
        // chatgptによる入力補完(completion)
        Gate::define('chat-gpt-completion', function (User $user, ChatGptUsageInformation $chat_gpt_usage_information) {
            // 1日のchatGPTAPI利用回数が残っているか
            return $chat_gpt_usage_information->remain_count > 0;
        });
    }
}
