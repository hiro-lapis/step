<?php
declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * クエリ実行リポジトリのDI設定プロバイダ
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $to_bind = [
            \App\Repositories\AchievementTimeType\AchievementTimeTypeRepositoryInterface::class => \App\Repositories\AchievementTimeType\AchievementTimeTypeRepository::class,
            \App\Repositories\Category\CategoryRepositoryInterface::class => \App\Repositories\Category\CategoryRepository::class,
            \App\Repositories\ChallengeStep\ChallengeStepRepositoryInterface::class => \App\Repositories\ChallengeStep\ChallengeStepRepository::class,
            \App\Repositories\Step\StepRepositoryInterface::class => \App\Repositories\Step\StepRepository::class,
            \App\Repositories\SubStep\SubStepRepositoryInterface::class => \App\Repositories\SubStep\SubStepRepository::class,
        ];
        foreach ($to_bind as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
