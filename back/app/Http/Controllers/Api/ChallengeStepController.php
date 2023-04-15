<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChallengeSteps\ClearRequest;
use App\Http\Requests\ChallengeSteps\ShowRequest;
use App\Services\ChallengeStepService;
use Illuminate\Http\JsonResponse;

class ChallengeStepController extends Controller
{
    private ChallengeStepService $challenge_step_service;

    public function __construct(ChallengeStepService $challenge_step_service)
    {
        $this->challenge_step_service = $challenge_step_service;
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  integer  $step_id
     * @return JsonResponse
     */
    public function show(ShowRequest $request, int $step_id): JsonResponse
    {
        $result = $this->challenge_step_service->show($step_id);
        return response()->json($result);
    }

    /**
     * ステップへのチャレンジ
     *
     *　@param ChallengeRequest $request
     * @return JsonResponse
     */
    public function clear(ClearRequest $request): JsonResponse
    {
        $result = $this->challenge_step_service->updateClear($request->validated()['id']);
        return response()->json(['message' => 'チャレンジ開始しました。達成するまでがんばりましょう！']);
    }
}
