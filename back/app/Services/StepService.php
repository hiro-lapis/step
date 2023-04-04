<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Step;
use App\Repositories\Step\StepRepositoryInterface;
use App\Repositories\SubStep\SubStepRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class StepService
{
    private StepRepositoryInterface $step_respository;
    private SubStepRepositoryInterface $sub_step_respository;

    public function __construct(
        StepRepositoryInterface $step_respository,
        SubStepRepositoryInterface $sub_step_respository,
    )
    {
        $this->step_respository = $step_respository;
        $this->sub_step_respository = $sub_step_respository;
    }

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
}
