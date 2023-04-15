<?php declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mypage\UpdateProfileRequest;
use App\Models\User;
use App\Services\StepService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MypageController extends Controller
{
    public function __construct(private StepService $step_service)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /** @ $user App/Models/User */
        $user = auth()->user();
        return compact('user');
    }

    /**
     * プロフィール情報の更新
     *
     * @param  UpdateProfileRequest  $request
     * @return JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = User::findOrFail($request->user()->id);

        $params = $request->validated();
        try {
            DB::beginTransaction();
            if ($request->file('file')) {
                // users/user_id/配下に顔図を配置
                $image_url = Storage::disk('s3')->put('public/users/' . (string)$request->user()->id, $request['file'], 'public');
                // 絶対パス(S3URL)を登録情報として保存
                $params['image_url'] = Storage::disk('s3')->url($image_url);
            }
            $user->fill($params)->update();
            DB::commit();
        } catch (\Exception $e) {
            report($e);
            DB::rollback();
        }
        $user->refresh();
        $message = 'プロフィール情報を更新しました';
        return response()->json(compact('user', 'message'));
    }

    public function postedStep(): JsonResponse
    {
        $result = $this->step_service->getPosted();
        return response()->json($result);
    }

    public function challengingStep(): JsonResponse
    {
        $result = $this->step_service->getChallenging();
        return response()->json($result);
    }
}
