<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\ChallengeStatusEnum;
use App\Models\Step;
use App\Repositories\ChallengeStep\ChallengeStepRepositoryInterface;
use App\Repositories\Step\StepRepositoryInterface;
use App\Repositories\SubStep\SubStepRepositoryInterface;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        try {
            DB::beginTransaction();
            // 登録時に署名付きURLが設定されないよう変数を分ける
            $image_url = $params['image_url'];
            unset($params['image_url']);
            // ステップ登録
            $step = $this->step_respository->create($params)->fresh();
            // 子ステップ登録
            $sub_step_params = collect($params['sub_steps'])
                ->filter(fn ($sub_step) => !empty($sub_step['name']) || !empty($sub_step['detail']))
                ->map(function ($sub_step) {
                    return [
                        'name' => $sub_step['name'] ?? '',
                        'detail' => $sub_step['detail'] ?? '',
                    ];
                });
            $count = $this->step_respository->updateOrCreateSubSteps($step, $sub_step_params);

            // 一時ディレクトリの画像をステップのディレクトリに保存
            $this->uploadAndUpdateImageUrl($step, $image_url);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        // 一時ディレクトリの画像を削除
        if (!App::environment('testing') && !empty($tmp_image_path)) {
            Storage::disk('s3')->delete($tmp_image_path);
        }
        return compact('step', 'status');
    }

    /**
     * ステップの更新(下書きの場合は公開状態にして更新)
     *
     * @param array $params
     * @return array
     */
    public function update(array $params): array
    {
        $status = Response::HTTP_OK;
        $message = '';
        DB::beginTransaction();
        try {
            // 更新時に署名付きURLが設定されないよう変数を分ける
            $image_url = $params['image_url'];
            unset($params['image_url']);
            // 著者でない時は更新不可
            $step = $this->step_respository->findOrFailByUserId($params['id'], auth()->user()->id);
            if (!Gate::allows('edit-step', $step)) {
                abort(HttpResponse::HTTP_FORBIDDEN);
            }
            // 非公開=>公開にして下書きを削除
            if ($step->is_active === false) {
                $params['is_active'] = true;
                $params['draft'] = null;
            }
            // ステップ更新
            $this->step_respository->update($step, collect($params)->except('sub_steps')->toArray());
            // 子ステップ更新
            $this->sub_step_respository->forceDeleteByStepId($step->id);
            $sub_step_params = collect($params['sub_steps'])
                ->filter(fn ($sub_step) => !empty($sub_step['name']) || !empty($sub_step['detail']))
                ->map(function ($sub_step) {
                    return [
                        'name' => $sub_step['name'] ?? '',
                        'detail' => $sub_step['detail'] ?? '',
                    ];
                });
            $count = $this->step_respository->updateOrCreateSubSteps($step, $sub_step_params);
            // 画像情報更新
            $this->uploadAndUpdateImageUrl($step, $image_url);
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
     * 新規作成の下書き保存
     *
     * @param array $params
     * @return array
     */
    public function storeDraft(array $params): array
    {
        $status = Response::HTTP_OK;
        // $params にid がなければ非公開状態でステップ情報を作成
        $exists = $params['id'] !== 0 && $this->step_respository->isExists($params['id'], auth()->user()->id);
        $image_url = $params['image_url'];
        // 画像情報を一時ディレクトリからステップのディレクトリに移動
        $params['image_url'] = $this->uploadAndUpdateImageUrl(null, $image_url);
        $json = collect($params)->except(['id'])->toJson(JSON_UNESCAPED_UNICODE);
        if (!$exists) {
            $step = $this->step_respository->createAsDraft(auth()->user()->id, $json);
        } else {
            $step = $this->step_respository->findOrFailByUserId($params['id'], auth()->user()->id);
            $this->step_respository->updateDraft($step, $json);
        }
        $step->refresh();
        // 既存の情報の下書き情報を更新
        return compact('step', 'status');
    }

    /**
     * 画像のアップロードとURL情報の更新
     *
     * @param Step|null $step
     * @param string|null $image_url
     * @param bool $is_draft (下書き保存時のみtrue)
     * @return string アップロードした画像のURL
     */
    private function uploadAndUpdateImageUrl(Step|null $step, string|null $image_url): string
    {
        // 画像未設定の場合は何もしない
        if (empty($image_url) || $step === null) return '';
        // 既存のステップ情報があり。更新前と変わらないURLの場合は何もしない
        if ($step->image_url === $image_url) return '';
        // NOTE: テスト環境の場合はS3関連処理をスキップしパラメータ情報そのままでDB更新
        if (App::environment('testing')) {
            $this->step_respository->update($step, ['image_url' => $image_url]);
            return '';
        }

        // 署名付きURLから画像パス、ファイル名を抽出
        $tmp_image_path = parse_url($image_url, PHP_URL_PATH);
        $tmp_data = Storage::disk('s3')->get($tmp_image_path);
        $file_name = Str::afterLast($tmp_image_path, '/');

        // ステップ用のディレクトリへファイルを保存し、URLをDBに登録
        Storage::disk('s3')->put("public/steps/{$step->id}/{$file_name}", $tmp_data, 'public');
        $image_url = Storage::disk('s3')->url("public/steps/{$step->id}/{$file_name}");

        if ($step !== null) {
            $this->step_respository->update($step, ['image_url' => $image_url]);
        }
        Storage::disk('s3')->delete($tmp_image_path);
        return $image_url;
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
            'achievement_time',
            'is_challenged',
            'is_cleared',
            'is_writer',
            'user_name',
            'user_image_url',
            'user_profile',
        ]);
    }

    /**
     * 編集対象の詳細情報を取得
     *
     * @param integer $step_id
     * @return Step $step
     */
    public function edit(int $step_id): Step
    {
        $step = $this->step_respository->findEditData($step_id, auth()->user()->id);

        // アクセサの設定
        $step->setAppends([
            'category_name',
            'achievement_time',
            'is_challenged',
            'is_cleared',
            'is_writer',
            'user_name',
            'user_image_url',
            'user_profile',
        ]);

        // 下書き中の場合は、draft設定をプロパティにセット
        if ($step->is_active === false) {
            $draft_data = json_decode($step->draft, true);
            foreach ($draft_data as $key => $value) {
                $step->$key = $value;
            }
            \Log::info('HIRO:draft_dataの中身' . print_r(($step->toArray()), true));
        }

        return $step;
    }

    /**
     * ステップ情報の論理削除
     *
     * @param array $params
     * @return array
     */
    public function delete(array $params): array
    {
        $step_id = $params['id'];
        $status = HttpResponse::HTTP_OK;
        $message = __('messages.delete_success');
        $step = $this->step_respository->findOrFailByUserId($step_id, auth()->user()->id);
        if (!Gate::allows('delete-step', $step)) {
            abort(HttpResponse::HTTP_FORBIDDEN, __('messages.step_delete_forbidden'));
        }
        try {
            DB::beginTransaction();
            $this->sub_step_respository->deleteByStepId($step->id);
            $this->step_respository->delete($step);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            $status = HttpResponse::HTTP_INTERNAL_SERVER_ERROR;
            $message = __('messages.database_error_has_occured');
        }
        return compact('status', 'message');
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
        $status = HttpResponse::HTTP_OK;
        $message = __('messages.challenge_started');
        $original_step = $this->step_respository->findShowData($step_id);
        if (!Gate::allows('store-challenge', $original_step)) {
            abort(HttpResponse::HTTP_FORBIDDEN, __('message.challenge_forbidden'));
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
            'time_count' => $original_step->time_count,
            'name' => $original_step->name,
            'image_url' => $original_step->image_url,
            'summary' => $original_step->summary,
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
                    'sort_number' => $sub_step->sort_number,
                ];
            })->toArray();
            $this->challenge_step_respository->createManySubStep($challenge_step, $original_sub_step_data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            $status = HttpResponse::HTTP_INTERNAL_SERVER_ERROR;
            $message = __('messages.database_error_has_occured');
        }
        $challenge_step_id = $challenge_step->id ?? null;
        return compact('status', 'message', 'challenge_step_id');
    }

    public function getPosted(): array
    {
        $steps = $this->step_respository->getByUserId(auth()->user()->id);
        $steps->each(fn ($step) => $step->setAppends([
            'category_name',
            'achievement_time',
            'is_challenged',
            'is_cleared',
        ]));
        return compact('steps');
    }

    public function getChallenging(): array
    {
        $steps = $this->challenge_step_respository->getByChallengeUserId(auth()->user()->id);
        return compact('steps');
    }
}
