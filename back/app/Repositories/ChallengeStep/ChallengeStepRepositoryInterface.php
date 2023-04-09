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

    /**
     * チャレンジ時のサブステップ情報を複数新規作成
     *
     * @param array $data
     * @return integer
     */
    public function createManySubStep(ChallengeStep $challenge_step, array $data): int;
}
