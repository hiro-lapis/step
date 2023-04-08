<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\ChallengeStep;
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
            $in_challenging = ChallengeStep::challengeUserId($user->id)->challenging()->exists();
            if ($in_challenging) return false;
            return true;
        });
    }
}
