<?php declare(strict_types=1);

namespace App\Repositories\Step;

use App\Models\Step;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * ステップ情報を検索しページネーションで取得
     *
     * @param array $condition
     * @return LengthAwarePaginator
     */
    public function pagenateByCondition(array $condition): LengthAwarePaginator;
}
