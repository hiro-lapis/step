<?php declare(strict_types=1);

namespace App\Repositories\ChallengeStep;

use App\Models\ChallengeStep;

class ChallengeStepRepository implements ChallengeStepRepositoryInterface
{
    public function __construct(
        private ChallengeStep $challenge_step,
    )
    { }

    /**
     * チャレンジ情報を新規作成
     *
     * @param array $data
     * @return ChallengeStep
     */
    public function create(array $data): ChallengeStep
    {
        return $this->challenge_step->create($data);
    }
}