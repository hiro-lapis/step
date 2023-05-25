<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Steps\ChallengeRequest;
use App\Http\Requests\Steps\CompletionRequest;
use App\Http\Requests\Steps\CreateRequest;
use App\Http\Requests\Steps\DeleteRequest;
use App\Http\Requests\Steps\UpdateRequest;
use App\Http\Requests\Steps\IndexRequest;
use App\Http\Requests\Steps\ShowRequest;
use App\Http\Requests\Steps\EditRequest;
use App\Http\Requests\Steps\StoreDraftRequest;
use App\Services\ChatGptService;
use App\Services\StepService;
use Illuminate\Http\JsonResponse;

class StepController extends Controller
{
    public function __construct(
        private StepService $step_service,
        private ChatGptService $chat_gpt_service
        )
    {}

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
     * Store a newly created resource in storage.
     *
     * @param  StoreDraftRequest $request
     * @return \Illuminate\Http\Response
     */
    public function storeDraft(StoreDraftRequest $request): JsonResponse
    {
        $result = $this->step_service->storeDraft($request->validated());
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
     * Display the specified resource.
     *
     * @param  EditRequest  $request
     * @return JsonResponse
     */
    public function edit(EditRequest $request): JsonResponse
    {
        $result = $this->step_service->edit($request->validated()['id']);
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
        return response()->json(['message' => $result['message'], 'challenge_step_id' => $result['challenge_step_id']], $result['status']);
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

    /**
     * chat GPT API 実行による入力補完
     *
     * @param CompletionRequest $request
     * @return JsonResponse
     */
    public function completion(CompletionRequest $request): JsonResponse
    {
        $result = $this->chat_gpt_service->completion($request->validated());
        return response()->json(
            ['message' => $result['message'], 'remain_count' => $result['remain_count']],
            $result['status']
        );
    }
}
