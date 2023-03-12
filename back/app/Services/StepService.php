<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Step;
use App\Repositories\Step\StepRepositoryInterface;

class StepService
{
    private StepRepositoryInterface $step_respository;

    public function __construct(StepRepositoryInterface $step_respository)
    {
        $this->step_respository = $step_respository;
    }

    public function store(array $params): Step
    {
        // ログインユーザーIDを追加
        $params['user_id'] = auth()->user()->id;
        return $this->step_respository->create($params);
    }
}
