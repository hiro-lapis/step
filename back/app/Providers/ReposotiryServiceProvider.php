<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * クエリ実行リポジトリのDI設定プロバイダ
 */
class ReposotiryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $to_bind = [
            \App\Repositories\Category\CategoryInterface::class => \App\Repositories\Category\CategoryRepository::class,
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
