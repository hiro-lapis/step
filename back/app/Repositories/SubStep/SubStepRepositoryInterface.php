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
}
