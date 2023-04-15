<?php declare(strict_types=1);

namespace App\Repositories\ChallengeStep;

use App\Models\ChallengeStep;
use Illuminate\Support\Collection;

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

    /**
     * ユーザーがチャレンジしているステップ情報を取得
     *
     * @param integer $user_id
     * @return Collection
     */
    public function getByChallengeUserId(int $user_id): Collection;

    /**
     * チャレンジ詳細画面に表示するデータを取得
     *
     * @param integer $step_id
     * @return ChallengeStep
     */
    public function findShowData(int $step_id): ChallengeStep;
}
