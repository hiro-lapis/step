<?php
declare(strict_types=1);

namespace App\Repositories\Step;

use App\Models\Step;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        // 並び順指定のためサブステップ数をサブクエリで取得
        $sub_step_count = DB::table('sub_steps')
            ->select('step_id', DB::raw('count(*) as sub_steps_count'))
            ->groupBy('step_id');
        $query = $this->step->query()
            ->isActive()
            ->joinMasterTables()
            ->leftJoinSub($sub_step_count, 'sub_step', function ($join) {
                $join->on('steps.id', '=', 'sub_step.step_id');
            })
            ->joinUsers();
        // キーワード(ステップ名,カテゴリー,ユーザー名)
        if (isset($condition['key_word'])) {
            $key_word = $condition['key_word'];
            $query->where(function ($query) use ($key_word) {
                $query->orWhere('steps.name', 'like', '%' . $key_word . '%');
                $query->orWhere('users.name', 'like', '%' . $key_word . '%');
            });
        }
        // カテゴリー
        if (isset($condition['category_id'])) {
            $query->where('steps.category_id', $condition['category_id']);
        }
        // 並び順
        if (isset($condition['order_by'])) {
            $order_by = $condition['order_by'];
            $desc = $condition['desc'] ?? false;
            $query->orderBy($order_by, $desc ? 'desc' : 'asc');
            // 達成目安時間の時はtime_countを第二ソートキーにする
            if ($order_by === 'achievement_time_types.sort_number') {
                $query->orderBy('steps.time_count', $desc ? 'desc' : 'asc');
            }
        }

        return $query->with('category:id,name')
            ->with('achievementTimeType:id,display_name')
            ->addSelect('steps.*')
            ->addSelect('sub_steps_count') // sort key 設定項目のため別名禁止
            ->paginate(10);
    }

    /**
     * 詳細画面(公開中)の情報取得

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
            ->isActive()
            ->find($step_id);
    }

    /**
     * 編集画面(公開/非公開両方)の情報取得
     *
     * @param int $step_id
     * @return Step
     */
    public function findEditData(int $step_id, int $user_id): Step
    {
        return $this->step
        ->with('category')
        ->with('user')
        // 並び順に取得
        ->with('subSteps')
        ->writerUser($user_id)
        ->find($step_id);
    }

    /**
     * 詳細画面(非公開)の情報取得

     * @param int $step_id
     * @return Step
     */
    public function findNotActiveData(int $step_id): Step
    {
        return $this->step
            ->with('category')
            ->with('user')
            // 並び順に取得
            ->with('subSteps')
            ->isNotActive()
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

    /**
     * ステップの存在確認
     *
     * @param integer $step_id
     * @param integer $user_id
     * @return boolean
     */
    public function isExists(int $step_id, int $user_id): bool
    {
        return $this->step->writerUser($user_id)->where('steps.id', $step_id)->exists();
    }

    /**
     * ステップを下書きとして非公開で新規登録
     *
     * @param int $user_id
     * @param string $json
     * @return Step
     */
    public function createAsDraft(int $user_id, string $json): Step
    {
        return $this->step->create([
            'user_id' => $user_id,
            'draft' => $json,
            'is_active' => false,
        ]);
    }

    /**
     * ステップの下書き情報を保存
     *
     * @param Step $step
     * @param string $json
     * @return bool
     */
    public function updateDraft(Step $step, string $json): bool
    {
        // 引数にとった配列(サブステップ込)をJSON形式に変換して保存
        $step->draft = $json;
        return $step->save();
    }
}
