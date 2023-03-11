<?php declare(strict_types=1);

namespace App\Repositories\AchievementTimeType;

use App\Models\AchievementTimeType;
use Illuminate\Database\Eloquent\Collection;

class AchievementTimeTypeRepository implements AchievementTimeTypeRepositoryInterface
{
    private AchievementTimeType $achievement_time_type;

    public function __construct(AchievementTimeType $achievement_time_type)
    {
        $this->achievement_time_type = $achievement_time_type;
    }

    public function get(): Collection
    {
        return $this->achievement_time_type->orderBy('sort_number')->get();
    }
}
