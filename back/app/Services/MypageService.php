<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\Step\StepRepositoryInterface;
use App\Repositories\SubStep\SubStepRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    public function update(array $params)
    {
        $user = User::findOrFail(auth()->user()->id);

        try {
            DB::beginTransaction();
            if ($params('file')) {
                // users/user_id/配下に顔図を配置
                $image_url = Storage::disk('s3')->put('public/users/' . (string)auth()->user()->id, $params['file'], 'public');
                $params['image_url'] = $image_url;
            }
            $user->fill($params)->update();
            DB::commit();
        } catch (\Exception $e) {
            report($e);
            DB::rollback();
        }
    }
}
