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
}
