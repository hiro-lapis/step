<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Steps\ChallengeRequest;
use App\Http\Requests\Steps\CreateRequest;
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
        return response()->json($result);
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
        return response()->json(['message' => 'チャレンジ開始しました。達成するまでがんばりましょう！']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
