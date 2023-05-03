<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\ChallengeStatusEnum;
use App\Models\ChallengeStep;
use App\Models\Step;
use App\Repositories\ChallengeStep\ChallengeStepRepositoryInterface;
use App\Repositories\Step\StepRepositoryInterface;
use App\Repositories\SubStep\SubStepRepositoryInterface;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class StepService
{
    public function __construct(
        private StepRepositoryInterface $step_respository,
        private SubStepRepositoryInterface $sub_step_respository,
        private ChallengeStepRepositoryInterface $challenge_step_respository,
    )
    {}

    /**
     * ステップの検索と取得
     *
     * @param array $params
     * @return array
     */
    public function get(array $params): array
    {
        $result = $this->step_respository->pagenateByCondition($params);
        return compact('result');
    }

    public function store(array $params): array
    {
        // ログインユーザーIDを追加
        $params['user_id'] = auth()->user()->id;
        $step = null;
        $status = Response::HTTP_OK;
        DB::beginTransaction();
        try {
            // ステップ登録
            $step = $this->step_respository->create($params)->fresh();
            // 子ステップ登録
            $sub_step_params = collect($params['sub_steps']);
            $count = $this->step_respository->updateOrCreateSubSteps($step, $sub_step_params);
            if ($count !== $sub_step_params->count()) throw new Exception('子ステップの更新件数が一致しません');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        return compact('step', 'status');
    }

    /**
     * ステップの更新
     *
     * @param array $params
     * @return array
     */
    public function update(array $params): array
    {
        $status = Response::HTTP_OK;
        DB::beginTransaction();
        try {
            // 著者でない時は更新不可
            $step = $this->step_respository->findOrFailByUserId($params['id'], auth()->user()->id);
            if (!Gate::allows('edit-step', $step)) {
                abort(HttpResponse::HTTP_FORBIDDEN);
            }
            // ステップ更新
            $this->step_respository->update($step, collect($params)->except('sub_steps')->toArray());
            // 子ステップ更新
            $this->sub_step_respository->deleteByStepId($step->id);
            $sub_step_params = collect($params['sub_steps']);
            $count = $this->step_respository->updateOrCreateSubSteps($step, $sub_step_params);
            if ($count !== $sub_step_params->count()) throw new Exception('子ステップの更新件数が一致しません');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        // 詳細情報取得
        $step = $this->show($step->id);
        return compact('step', 'status');
    }

    /**
     * 詳細情報を取得
     *
     * @param integer $step_id
     * @return void
     */
    public function show(int $step_id): Step
    {
        $step = $this->step_respository->findShowData($step_id);
        // アクセサの設定
        return $step->setAppends([
            'category_name',
            'achievement_time_type_name',
            'is_writer',
            'user_name',
            'user_image_url',
            'user_profile',
        ]);
    }

    /**
     * ステップへのチャレンジ
     *
     * @param integer $step_id
     * @return array
     * @throws HttpException
     */
    public function challenge(int $step_id): array
    {
        $original_step = $this->step_respository->findShowData($step_id);
        if (!Gate::allows('store-challenge', $original_step)) {
            abort(HttpResponse::HTTP_FORBIDDEN);
        }

        // チャレンジ時点のステップ情報を保存しつつチャレンジ情報作成
        // ChallengeStepのライフサイクルメソッドでチャレンジ状況も自動更新
        $data = [
            'challenge_user_id' => auth()->user()->id,
            'step_id' => $original_step->id,
            'challenged_at' => now(),
            'status' => ChallengeStatusEnum::Challenging->value,
            'post_user_id' => $original_step->user_id,
            'category_id' => $original_step->category_id,
            'achievement_time_type_id' => $original_step->achievement_time_type_id,
            'name' => $original_step->name,
            'image_url' => $original_step->image_url,
            'summary' => $original_step->summary,
            'merit' => $original_step->merit,
        ];

        try {
            DB::beginTransaction();
            $challenge_step = $this->challenge_step_respository->create($data);
            // チャレンジ時のサブステップデータの作成
            $original_sub_step_data = $original_step->subSteps->map(function ($sub_step) use ($challenge_step) {
                return [
                    'challenge_step_id' => $challenge_step->id,
                    'sub_step_id' => $sub_step->id,
                    'challenged_at' => $sub_step->sort_number == 1 ? now() : null, // 最初の子ステップのみチャレンジ状態にする
                    'status' => $sub_step->sort_number == 1 ? ChallengeStatusEnum::Challenging->value : ChallengeStatusEnum::NotChallenged->value, // 最初の子ステップのみチャレンジ状態にする
                    'name' => $sub_step->name,
                    'detail' => $sub_step->detail,
                    'image_url' => $sub_step->image_url,
                    'sort_number' => $sub_step->sort_number,
                ];
            })->toArray();
            $this->challenge_step_respository->createManySubStep($challenge_step, $original_sub_step_data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            throw new HttpException(HttpResponse::HTTP_INTERNAL_SERVER_ERROR, 'チャレンジデータの作成に失敗しました');
        }

        return compact('challenge_step');
    }

    public function getPosted(): array
    {
        $steps = $this->step_respository->getByUserId(auth()->user()->id);
        $steps->each(fn ($step) => $step->setAppends(['category_name', 'achievement_time_type_name']));
        return compact('steps');
    }

    public function getChallenging(): array
    {
        $steps = $this->challenge_step_respository->getByChallengeUserId(auth()->user()->id);
        $steps->each(fn ($step) => $step->setAppends(['category_name', 'achievement_time_type_name', 'status_name']));
        return compact('steps');
    }
}
