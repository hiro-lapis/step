<?php declare(strict_types=1);

namespace App\Repositories\ChallengeSubStep;

use App\Enums\ChallengeStatusEnum;
use App\Models\ChallengeSubStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ItemNotFoundException;

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

    /**
     * 条件に合致するチャレンジサブステップを取得
     * @params $params
     * @return Model
     * @throws ItemNotFoundException
     */
    public function firstOrFailByParams(array $params): Model
    {
        $query = $this->challenge_sub_step->query();
        return $query->join('challenge_steps', function ($query) use ($params) {
            $query->on('challenge_steps.id', '=', 'challenge_sub_steps.challenge_step_id')
                // 挑戦しているユーザーIDを条件に絞り込み
                ->when(isset($params['challenge_user_id']), fn($q) => $q->where('challenge_user_id', $params['challenge_user_id']));
                // ->when(isset($params['challenge_sub_step_id']), fn($q) => $q->where('challenge_sub_steps.id', $params['challenge_sub_step_id']));
        })
            ->challenging()
            ->with('challengeStep', function($query) {
                $query->withCount('challengeSubSteps');
            })
            ->firstOrFail();
    }
}
