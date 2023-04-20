<?php declare(strict_types=1);

namespace App\Repositories\ChallengeSubStep;

use App\Enums\ChallengeStatusEnum;
use App\Models\ChallengeSubStep;
use Illuminate\Database\Eloquent\Model;

interface ChallengeSubStepRepositoryInterface
{
    /**
     * チャレンジ状況の更新
     *
     * @param ChallengeStatusEnum $status
     */
    public function updateStatus(int $challenge_sub_step_id, ChallengeStatusEnum $status);

    /**
     * 条件に合致するチャレンジサブステップを取得
     * @params $params
     * @return Model
     * @throws ItemNotFoundException
     */
    public function firstOrFailByParams(array $params): Model;
}
