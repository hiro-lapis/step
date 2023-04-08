<?php declare(strict_types=1);

namespace App\Repositories\ChallengeStep;

use App\Models\ChallengeStep;

interface ChallengeStepRepositoryInterface
{
    /**
     * チャレンジ情報を新規作成
     *
     * @param array $data
     * @return ChallengeStep
     */
    public function create(array $data): ChallengeStep;
}
