<?php declare(strict_types=1);

namespace App\Repositories\ChallengeStep;

use App\Models\ChallengeStep;
use Illuminate\Support\Collection;

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

    /**
     * 引数にとったチャレンジステップ情報に紐づくサブステップ情報を新規作成
     *
     * @param ChallengeStep $challenge_step
     * @param array $data
     * @return integer
     */
    public function createManySubStep(ChallengeStep $challenge_step, array $data): int
    {
         $challenge_step->challengeSubSteps()->createMany($data);
         return $challenge_step->challengeSubSteps()->count();
    }

public function getByChallengeUserId(int $user_id): Collection
    {
        return $this->challenge_step->where('challenge_user_id', $user_id)
            ->with(['challengeSubSteps', 'category', 'achievementTimeType'])
            ->withCount('challengeSubSteps')
            ->withCount('clearedSubSteps')
            ->get();
    }

    public function findShowData(int $step_id): ChallengeStep
    {
        return $this->challenge_step
            ->with('category')
            ->with('postUser')
            ->with('challengeSubSteps')
            ->withCount('clearedSubSteps')
            ->find($step_id);
    }
}
