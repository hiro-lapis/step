<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\ChallengeStatusEnum;
use App\Models\ChallengeStep;
use App\Models\Step;
use App\Repositories\ChallengeStep\ChallengeStepRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ChallengeStepService
{
    public function __construct(
        private ChallengeStepRepositoryInterface $challenge_step_respository,
    )
    {}

    /**
     * 詳細情報を取得
     *
     * @param integer $step_id
     * @return ChallengeStep
     */
    public function show(int $step_id): ChallengeStep
    {
        $step = $this->challenge_step_respository->findShowData($step_id);
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
     * ステップへのチャレンジ
     *
     * @param integer $step_id
     * @return array
     * @throws HttpException
     */
    // public function challenge(int $step_id): array
    // {
    //     $original_step = $this->step_respository->findShowData($step_id);
    //     if (!Gate::allows('store-challenge', $original_step)) {
    //         abort(HttpResponse::HTTP_FORBIDDEN);
    //     }

    //     // チャレンジ時点のステップ情報を保存しつつチャレンジ情報作成
    //     // ChallengeStepのライフサイクルメソッドでチャレンジ状況も自動更新
    //     $data = [
    //         'challenge_user_id' => auth()->user()->id,
    //         'step_id' => $original_step->id,
    //         'challenged_at' => now(),
    //         'status' => ChallengeStatusEnum::Challenging->value,
    //         'post_user_id' => $original_step->user_id,
    //         'category_id' => $original_step->category_id,
    //         'achievement_time_type_id' => $original_step->achievement_time_type_id,
    //         'name' => $original_step->name,
    //         'image_url' => $original_step->image_url,
    //         'summary' => $original_step->summary,
    //         'merit' => $original_step->merit,
    //     ];

    //     try {
    //         DB::beginTransaction();
    //         $challenge_step = $this->challenge_step_respository->create($data);
    //         // チャレンジ時のサブステップデータの作成
    //         $original_sub_step_data = $original_step->subSteps->map(function ($sub_step) use ($challenge_step) {
    //             return [
    //                 'challenge_step_id' => $challenge_step->id,
    //                 'sub_step_id' => $sub_step->id,
    //                 'challenged_at' => $sub_step->sort_number == 1 ? now() : null, // 最初の子ステップのみチャレンジ状態にする
    //                 'status' => $sub_step->sort_number == 1 ? ChallengeStatusEnum::Challenging->value : ChallengeStatusEnum::NotChallenged->value, // 最初の子ステップのみチャレンジ状態にする
    //                 'name' => $sub_step->name,
    //                 'detail' => $sub_step->detail,
    //                 'image_url' => $sub_step->image_url,
    //                 'sort_number' => $sub_step->sort_number,
    //             ];
    //         })->toArray();
    //         $this->challenge_step_respository->createManySubStep($challenge_step, $original_sub_step_data);
    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         report($e);
    //         throw new HttpException(HttpResponse::HTTP_INTERNAL_SERVER_ERROR, 'チャレンジデータの作成に失敗しました');
    //     }

    //     return compact('challenge_step');
    // }

    // public function getPosted(): array
    // {
    //     $steps = $this->step_respository->getByUserId(auth()->user()->id);
    //     $steps->each(fn ($step) => $step->setAppends(['category_name', 'achievement_time_type_name']));
    //     return compact('steps');
    // }

    // public function getChallenging(): array
    // {
    //     $steps = $this->challenge_step_respository->getByChallengeUserId(auth()->user()->id);
    //     $steps->each(fn ($step) => $step->setAppends(['category_name', 'achievement_time_type_name', 'status_name']));
    //     return compact('steps');
    // }
}
