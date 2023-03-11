<?php declare(strict_types=1);

namespace App\Repositories\Step;

use App\Models\Step;
use Illuminate\Support\Collection;

interface StepRepositoryInterface
{
    /**
     * ステップ情報を新規登録
     *
     * @params array $params
     * @return Step
     */
    public function store(array $params): Step;
}
