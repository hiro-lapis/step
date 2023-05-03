<?php
declare(strict_types=1);

namespace App\Repositories\SubStep;

use App\Models\SubStep;

class SubStepRepository implements SubStepRepositoryInterface
{
    public function __construct(
        private SubStep $sub_step,
    )
    {}

    public function create(array $params): SubStep
    {
        return $this->sub_step->create($params);
    }

    public function deleteByStepId(int $step_id): int
    {
        return $this->sub_step->where('step_id', $step_id)->delete();
    }
}
