<?php
declare(strict_types=1);

namespace App\Repositories\Step;

use App\Models\Step;

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
}
