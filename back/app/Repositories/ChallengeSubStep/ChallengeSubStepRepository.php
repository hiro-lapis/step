<?php declare(strict_types=1);

namespace App\Repositories\ChallengeSubStep;

use App\Enums\ChallengeStatusEnum;
use App\Models\ChallengeSubStep;

class ChallengeSubStepRepository implements ChallengeSubStepRepositoryInterface
{
    public function __construct(
        private ChallengeSubStep $challenge_sub_step,
    )
    { }

    /**
     * チャレンジ状況の更新
     *
     * @param integer $challenge_sub_step_id
     * @param ChallengeStatusEnum $status
     */
    public function updateStatus(int $challenge_sub_step_id, ChallengeStatusEnum $status)
    {
        /** @var ChallengeSubStep $challenge_sub_step */
        $challenge_sub_step = $this->challenge_sub_step->findOrFail($challenge_sub_step_id);
        $challenge_sub_step->status = $status;
        if ($status === ChallengeStatusEnum::Cleared) {
            $challenge_sub_step->cleared_at = now();
        }
        $challenge_sub_step->save();
        return $challenge_sub_step;
    }
}
