<?php declare(strict_types=1);

namespace App\Repositories\AchievementTimeType;

use Illuminate\Support\Collection;

interface AchievementTimeTypeRepositoryInterface
{
    /**
     * 達成目安時間を一覧取得
     *
     * @return Collection
     */
    public function get(): Collection;
}
