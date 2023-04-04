<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Steps\ChallengeRequest;
use App\Http\Requests\Steps\CreateRequest;
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
     * @return \Illuminate\Http\Response
     */
    public function show(ShowRequest $request): JsonResponse
    {
        $result = $this->step_service->show($request->validated()['id']);
        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * ステップへのチャレンジ
     *
     *
     * @return JsonResponse
     */
    public function challenge(ChallengeRequest $request): JsonResponse
    {
        return response()->json(['result' => true]);
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
