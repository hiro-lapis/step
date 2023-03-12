<?php
declare(strict_types=1);

namespace App\Repositories\Step;

use App\Models\Step;
use Illuminate\Pagination\LengthAwarePaginator;

class StepRepository implements StepRepositoryInterface
{
    private Step $step;

    public function __construct(Step $step)
    {
        $this->step = $step;
    }

    public function create(array $params): Step
    {
        return $this->step->create($params);
    }


    /**
     * ステップ情報を検索しページネーションで取得
     *
     * @param array $condition
     * @return LengthAwarePaginator
     */
    public function pagenateByCondition(array $condition): LengthAwarePaginator
    {
        $query = $this->step->query()
            ->joinMasterTables()
            ->joinUsers();
        // キーワード(ステップ名,カテゴリー,ユーザー名)
        if ($condition['key_word']) {
            $query->where(function ($query) use ($condition) {
                $query->orWhere('steps.name', 'like', '%' . $condition['key_word'] . '%');
                $query->orWhere('categories.name', 'like', '%' . $condition['key_word'] . '%');
                $query->orWhere('users.name', 'like', '%' . $condition['key_word'] . '%');
            });
        }
        // カテゴリー選択
        if (array_key_exists('category_id', $condition)) {
            if (count($condition['category_id']) > 0) {
                $query->where('steps.category_id', $condition['category_ids']);
            }
        }
        // 達成目安時間
        if ($condition['city_id']) {
            $query->where('shops.achievement_time_type_id', $condition['achievement_time_type_id']);
        }
        return $query->with('category:id,name')
            ->with('achievementTimeType:id,name')
            ->addSelect('steps.*')
            ->paginate();
    }
}
