<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Steps\ChallengeRequest;
use App\Http\Requests\Steps\CreateRequest;
use App\Http\Requests\Steps\DeleteRequest;
use App\Http\Requests\Steps\UpdateRequest;
use App\Http\Requests\Steps\IndexRequest;
use App\Http\Requests\Steps\ShowRequest;
use App\Services\StepService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StepController extends Controller
{
    private StepService $step_service;

    public function __construct(StepService $step_service)
    {
        $this->step_service = $step_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexRequest $request)
    {
        return $this->step_service->get($request->validated());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request): JsonResponse
    {
        $result = $this->step_service->store($request->validated());
        return response()->json($result['step'], $result['status']);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @return JsonResponse
     */
    public function show(ShowRequest $request): JsonResponse
    {
        $result = $this->step_service->show($request->validated()['id']);
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest $request
     * @return JsonResponse
     */
    public function update(UpdateRequest $request): JsonResponse
    {
        $result = $this->step_service->update($request->validated());
        return response()->json(['step' => $result['step']], $result['status']);
    }

    /**
     * ステップへのチャレンジ
     *
     *　@param ChallengeRequest $request
     * @return JsonResponse
     */
    public function challenge(ChallengeRequest $request): JsonResponse
    {
        $result = $this->step_service->challenge($request->validated()['id']);
        return response()->json(['message' => $result['message']], $result['status']);
    }

    /**
     * ステップ投稿者によるステップ情報の削除
     *
     * @param  int  $id
     * @return DeleteRequest
     */
    public function destroy(DeleteRequest $request)
    {
        $result = $this->step_service->delete($request->validated());
        return response()->json(['message' => $result['message']], $result['status']);
    }
}
