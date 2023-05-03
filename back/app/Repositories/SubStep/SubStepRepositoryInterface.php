<?php declare(strict_types=1);

namespace App\Repositories\SubStep;

use App\Models\SubStep;

interface SubStepRepositoryInterface
{
    /**
     * 子ステップ情報を新規登録
     *
     * @params array $params
     * @return SubStep
     */
    public function create(array $params): SubStep;

    /**
     * 子ステップ情報を削除
     *
     * @param integer $step_id
     * @return int
     */
    public function deleteByStepId(int $step_id): int;

    /**
     * 子ステップ情報を物理削除
     *
     * @param integer $step_id
     * @return int
     */
    public function forceDeleteByStepId(int $step_id): int;
}
