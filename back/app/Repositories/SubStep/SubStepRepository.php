<?php
declare(strict_types=1);

namespace App\Repositories\SubStep;

use App\Models\SubStep;

class SubStepRepository implements SubStepRepositoryInterface
{
    private SubStep $sub_step;

    public function __construct(SubStep $sub_step)
    {
        $this->sub_step = $sub_step;
    }

    public function create(array $params): SubStep
    {
        return $this->sub_step->create($params);
    }
}
