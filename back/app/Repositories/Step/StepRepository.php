<?php
declare(strict_types=1);

namespace App\Repositories\Step;

use App\Models\Step;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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

    public function updateOrCreateSubSteps(Step $step, Collection $sub_step_params): int
    {
        $sort_number = 1;
        $sub_step_params->each(function($params) use ($step, &$sort_number) {
            // steps.idとsort_numberで検索をかけてupsertをかける
            $step->subSteps()->updateOrCreate(
                ['step_id' => $step->id, 'sort_number' => $sort_number],
                $params
            );
            $sort_number++;
        });
        // 処理結果件数としてsort_numberを返却
        return ($sort_number - 1);
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
        if (isset($condition['key_word'])) {
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
        if (array_key_exists('achievement_time_type_id', $condition)) {
            $query->where('shops.achievement_time_type_id', $condition['achievement_time_type_id']);
        }
        return $query->with('category:id,name')
            ->with('achievementTimeType:id,name')
            ->addSelect('steps.*')
            ->paginate();
    }

    /**
     * 詳細画面の情報取得

     * @param int $step_id
     * @return Step
     */
    public function findShowData(int $step_id): Step
    {
        return $this->step
            ->with('category')
            ->with('user')
            // 並び順に取得
            ->with('subSteps', fn($query) => $query->orderBy('sort_number', 'asc'))
            ->find($step_id);
    }
}
