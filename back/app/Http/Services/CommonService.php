<?php declare(strict_types=1);

namespace App\Http\Services;

use App\Repositories\AchievementTimeType\AchievementTimeTypeRepositoryInterface;
use App\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\Support\Collection;

class CommonService
{
    private CategoryRepositoryInterface $category_repository;
    private AchievementTimeTypeRepositoryInterface $achievement_time_type_repository;

    public function __construct(
        CategoryRepositoryInterface $category_repository,
        AchievementTimeTypeRepositoryInterface $achievement_time_type_repository,
    )
    {
        $this->category_repository = $category_repository;
        $this->achievement_time_type_repository = $achievement_time_type_repository;
    }

    /**
     * カテゴリー情報の取得
     *
     * @return Collection
     */
    public function getCategory(): Collection
    {
        return $this->category_repository->get();
    }

    /**
     * 達成目安時間情報の取得
     *
     * @return Collection
     */
    public function getAchievementTimeType(): Collection
    {
        return $this->achievement_time_type_repository->get();
    }
}
