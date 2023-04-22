<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\ChallengeStatusEnum;
use App\Models\ChallengeStep;
use App\Repositories\ChallengeStep\ChallengeStepRepositoryInterface;
use App\Repositories\ChallengeSubStep\ChallengeSubStepRepositoryInterface;
use App\Repositories\Step\StepRepositoryInterface;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ChallengeStepService
{
    public function __construct(
        private StepRepositoryInterface             $step_respository,
        private ChallengeStepRepositoryInterface    $challenge_step_repository,
        private ChallengeSubStepRepositoryInterface $challenge_sub_step_respository,
    )
    {
    }

    /**
     * 詳細情報を取得
     *
     * @param integer $step_id
     * @return ChallengeStep
     */
    public function show(int $step_id): ChallengeStep
    {
        $step = $this->challenge_step_repository->findShowData($step_id, auth()->id());
        // アクセサの設定
        return $step->setAppends([
            'category_name',
            'achievement_time_type_name',
            'post_user_name',
            'post_user_image_url',
            'post_user_profile',
        ]);
    }

    /**
     * チャレンジステップのクリア
     * サブステップを達成済にし、状態に応じてステップも更新
     *
     * @param array $params
     * @return string クリア結果メッセージ
     * @throws HttpException
     */
    public function updateClear(array $params): string
    {

        $challenge_step = $this->challenge_step_repository
            ->findOrFailInChallengeStep(
                $params['challenge_step_id'],
                $params + ['challenge_user_id' => auth()->user()->id]
            );
        // 更新するサブステップを取り出す
        $target_sub_step = $challenge_step
            ->challengeSubSteps
            ->where(function($sub_step) use ($params) {
                return $sub_step->id === $params['challenge_sub_step_id']
                    && $sub_step->status === ChallengeStatusEnum::Challenging->value;
            });
        // 存在しなかったらNOT FOUND
        if ($target_sub_step->count() === 0) {
            abort(404);
        }
        $target_sub_step = $target_sub_step->first();

        // 次のサブステップを取得
        $next_sub_step_sort_number = $target_sub_step->sort_number +1;
        $next_sub_steps = $challenge_step
            ->challengeSubSteps
            ->where(function ($sub_step) use ($next_sub_step_sort_number) {
                return $sub_step->sort_number === $next_sub_step_sort_number;
            });
        try {
            DB::beginTransaction();
            // 対象のサブステップを更新
            $target_sub_step->update([
                'cleared_at' => now(),
                'status' => ChallengeStatusEnum::Cleared->value
            ]);
            $next_sub_step_sort_number = $target_sub_step->sort_number +1;
            // 次のサブステップを取得
            $next_sub_steps = $challenge_step
                ->challengeSubSteps
                ->where(function ($sub_step) use ($next_sub_step_sort_number) {
                    return $sub_step->sort_number === $next_sub_step_sort_number;
                });
            // 次のサブステップがある場合、チャンレジ開始
            $has_next_sub_step = $next_sub_steps->count() > 0;
            $next_challenge_sub_step_id = $has_next_sub_step ?
                    $next_sub_steps->first()->id : null;
            if ($has_next_sub_step) {
                \Log::info('HIRO:サブステップ更新');
                $next_sub_steps->first()->update([
                    'challenged_at' => now(),
                    'status' => ChallengeStatusEnum::Challenging->value,
                ]);
            } else {
                \Log::info('HIRO:親ステップ完了へ更新');
                // 親情報のステップ自体をクリア状態へ更新
                $challenge_step->update([
                    'cleared_at' => now(),
                    'status' => ChallengeStatusEnum::Cleared->value,
                ]);
            }

            DB::commit();
            // $challenge_step->refresh();
            // \Log::info('HIRO:resultの中身' . print_r($challenge_step->challengeSubSteps, true));
        } catch (\Exception $e) {
            report($e);
            throw new HttpException(HttpResponse::HTTP_BAD_REQUEST, '更新に失敗しました');
            DB::rollBack();
        }

        $message = $has_next_sub_step ? 'クリア情報を更新しました。次のステップに進みましょう！' : 'おめでとうございます、全てのステップを完了しました！';
        \Log::info('HIRO:messageの中身' . print_r($message, true));
        return $message;
    }

    /**
     * ユーザーIDからチャレンジ中のステップIDを取得
     *
     * @param integer $user_id
     * @return array
     */
    public function getUserChallengeStepIds(int $user_id): array
    {
        $challenge_step_ids = $this->challenge_step_repository
            ->getIdsByChallengeUserId($user_id)
            ->map(fn($challenge_step) => $challenge_step->step_id)
            ->flatten()
            ->toArray();
        return $challenge_step_ids;
    }
}
