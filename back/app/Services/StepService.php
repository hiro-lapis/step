<?php
declare(strict_types=1);

namespace App\Services;

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
            \Log::info('HIRO:stepの中身' . print_r($step->toArray(), true));
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
}
