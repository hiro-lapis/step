<?php
declare(strict_types=1);

namespace App\Repositories\Step;

use App\Models\Step;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class StepRepository implements StepRepositoryInterface
{
    public function __construct(
        private Step $step,
    )
    {}

    public function create(array $params): Step
    {
        return $this->step->create($params);
    }

    public function update(Step $step, array $params): bool
    {
        return $step->update($params);
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
            $key_word = $condition['key_word'];
            $query->where(function ($query) use ($key_word) {
                $query->orWhere('steps.name', 'like', '%' . $key_word . '%');
                $query->orWhere('categories.name', 'like', '%' . $key_word . '%');
                $query->orWhere('users.name', 'like', '%' . $key_word . '%');
            });
        }

        return $query->with('category:id,name')
            ->with('achievementTimeType:id,display_name')
            ->withCount('subSteps')
            ->addSelect('steps.*')
            ->paginate(10);
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
            ->with('subSteps')
            ->find($step_id);
    }

    public function findOrFailByUserId(int $step_id, int $user_id): Step
    {
        return $this->step->writerUser($user_id)->findOrFail($step_id);
    }

    /**
     * ユーザーIDを元にステップ情報を取得
     *
     * @param integer $user_id
     * @return Collection
     */
    public function getByUserId(int $user_id): Collection
    {
        return $this->step
            ->where('user_id', $user_id)
            ->with(['category:id,name', 'achievementTimeType:id,display_name'])
            ->withCount('subSteps')
            ->get();
    }

    public function delete(Step $step): bool
    {
        return $step->delete();
    }
}
