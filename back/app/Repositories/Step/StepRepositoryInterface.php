<?php declare(strict_types=1);

namespace App\Repositories\Step;

use App\Models\Step;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface StepRepositoryInterface
{
    /**
     * ステップ情報を新規登録
     *
     * @params array $params
     * @return Step
     */
    public function create(array $params): Step;

    /**
     * 引数にとったステップ情報を更新
     *
     * @params Step $step
     * @params array $params
     * @return bool
     */
    public function update(Step $step, array $params): bool;

    /**
     * 引数にとったステップ情報に紐づける形で子ステップを登録更新
     *
     * @param Step $step
     * @param Collection $sub_step_params
     * @return int 子ステップの登録更新回数
     */
    public function updateOrCreateSubSteps(Step $step, Collection $sub_step_params): int;

    /**
     * ステップ情報を検索しページネーションで取得
     *
     * @param array $condition
     * @return LengthAwarePaginator
     */
    public function pagenateByCondition(array $condition): LengthAwarePaginator;

    /**
     * 詳細画面(公開)の情報取得
     *
     * @param int $step_id
     * @return Step
     */
    public function findShowData(int $step_id): Step;

    /**
     * ステップIDとユーザーIDをもとにステップ情報の著者かどうかをチェック
     *
     * @param integer $step_id
     * @param integer $user_id
     * @return Step
     */
    public function findOrFailByUserId(int $step_id, int $user_id): Step;

    /**
     * ユーザーIDを元にステップ情報を取得
     *
     * @param integer $user_id
     * @return Collection
     */
    public function getByUserId(int $user_id): Collection;

    /**
     * ステップ情報を論理削除
     *
     * @param Step $step
     * @return bool
     */
    public function delete(Step $step): bool;

    /**
     * ステップの存在確認
     *
     * @param integer $step_id
     * @param integer $user_id
     * @return boolean
     */
    public function isExists(int $step_id, int $user_id): bool;

    /**
     * ステップを下書きとして非公開で新規登録
     *
     * @param int $user_id
     * @param string $json
     * @return Step
     */
    public function createAsDraft(int $user_id, string $json): Step;

    /**
     * ステップの下書き情報を保存
     *
     * @param Step $step
     * @param string $json
     * @return bool
     */
    public function updateDraft(Step $step, string $json): bool;
}
