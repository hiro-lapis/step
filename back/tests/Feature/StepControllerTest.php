<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ChallengeStatusEnum;
use App\Models\AchievementTimeType;
use App\Models\Category;
use App\Models\ChallengeInformation;
use App\Models\ChallengeStep;
use App\Models\ChatGptUsageInformation;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
// use OpenAI\Laravel\Facades\OpenAI;
// use OpenAI\Responses\Completions\CreateResponse;
// use OpenAI\Testing\ClientFake;

class StepControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;
    private AchievementTimeType $achievement_time_type;

    /**
     * テストユーザー作成処理
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->achievement_time_type = AchievementTimeType::factory()->create();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @return void
     */
    public function test_allowGuestUserAccess(): void
    {
        // 未ログイン状態でアクセス
        $response = $this->getJson('/api/steps');
        $response->assertOk();
        // ログイン状態でアクセス
        $response = $this->actingAs($this->user)->getJson('/api/steps');
        $response->assertOk();
    }

    /**
     * @return void
     */
    public function test_notAllowLoginUserAccess(): void
    {
        // ステップ新規登録API
        $response = $this->postJson('/api/steps', []);
        $response->assertUnauthorized();
        // ログイン状態でのアクセスを確認
        $response = $this->actingAs($this->user)->json('post', '/api/steps');
        $response->assertUnprocessable();
    }

    public function test_storeCorrectStepData(): void
    {
        // 登録前のデータ状況を確認
        $this->assertDatabaseCount('steps', 0);
        $sub_step_count = rand(1, 3);
        $sub_steps = [];
        for ($i=1; $i <= $sub_step_count; $i++) {
            $sub_steps[] = [
                'name' => 'サブステップ',
                'detail' => 'サブステップ詳細' . $i,
                'sort_number' => $i,
            ];
        }
        $params = [
            'name' => 'テストだよ',
            'category_id' => $this->category->id,
            'achievement_time_type_id' => $this->achievement_time_type->id,
            'summary' => 'テストサマリーだよ',
            'sub_steps' => $sub_steps,
        ];
        $this->actingAs($this->user)->postJson('/api/steps', $params);

        // 期待値の評価
        $this->assertDatabaseCount('steps', 1);
        $result = Step::first();
        $this->assertSame($params['name'], $result->name);
        $this->assertSame($params['summary'], $result->summary);
        $this->assertSame($this->user->id, $result->user_id);
        $this->assertSame($this->category->id, $result->category_id);
        $this->assertSame($params['achievement_time_type_id'], $result->achievement_time_type_id);
        // サブステップの個数
        $this->assertSame($sub_step_count, $result->subSteps()->count());
    }

    public function test_StepIndexReturnExpectedData(): void
    {
        // ユーザー2人それぞれステップを5件づつ登録
        User::factory()
            ->count(2)
            ->hasSteps(5, [
                'category_id' => $this->category->id,
                'achievement_time_type_id' => $this->achievement_time_type->id,
            ])
            ->create();
        $response = $this->getJson('/api/steps');
        $paginate = $response['result'];
        // 期待値の判定
        // 総件数
        $this->assertSame(10, $paginate['total']);
        //
        $this->assertSame(10, $paginate['total']);
    }

    public function test_stepShow(): void
    {
        $step_id = '1';
        $response = $this->getJson('/api/steps/' . $step_id);
        // 存在しないステップを参照するとバリデーションで弾かれるか
        $response->assertUnprocessable();

        // 子ステップ2つもつステップ情報を作成
        $step = Step::factory()
            ->has(SubStep::factory()->count(2))
            ->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'achievement_time_type_id' => $this->achievement_time_type->id,
        ]);
        $response = $this->getJson('/api/steps/' . $step->id);

        $step = Step::find($step->id);
        $response->assertJson([
            'id' => $step->id,
            'user_id' => $step->user_id,
            'category_id' => $step->category_id,
            'achievement_time_type_id' => $step->achievement_time_type_id,
            'name' => $step->name,
            'category_name' => $step->category_name,
            'user_name' => $step->user_name,
            'user_image_url' => $step->user_image_url,
            'achievement_time_type_name' => $step->achievement_time_type_name,
            'is_writer' => $step->is_writer,
        ]);
    }

    public function test_challenge(): void
    {
        // 自分が作者のステップ情報(子ステップ2つ)を作成 (sort_numberを設定しつつ作成)
        $step = Step::factory()
            ->has(SubStep::factory()->count(2)->sequence(
                ['sort_number' => 1],
                ['sort_number' => 2],
            ))
            ->create([
                'user_id' => $this->user->id,
                'category_id' => $this->category->id,
                'achievement_time_type_id' => $this->achievement_time_type->id,
            ]);

        // チャレンジ実行
        $response = $this->postJson('/api/steps/challenges');
        // 未ログインだと401になるか
        $response->assertUnauthorized();

        // ログイン状態でチャレンジ実行
        $response = $this->actingAs($this->user)->postJson('/api/steps/challenges', ['id' => $step->id]);
        // 自作のステップには挑戦できない403レスポンスが返ってくるか
        $response->assertForbidden();

        // ステップの投稿者を異なるユーザーにして再度チャレンジ実行
        $step->update(['user_id' => 999999999]);
        $response = $this->actingAs($this->user)->postJson('/api/steps/challenges', ['id' => $step->id]);
        $response->assertOk();
        // 挑戦中のチャレンジデータが作成されているか
        $this->assertTrue(ChallengeStep::where('challenge_user_id', $this->user->id)->where('step_id', $step->id)->where('status', ChallengeStatusEnum::Challenging)->exists());

        // ユーザーのチャレンジ関連情報が存在するか
        $challenge = ChallengeInformation::where('user_id', $this->user->id)->first();
        $this->assertSame(1, $challenge->challenge_count);
        $this->assertSame(1, $challenge->challenging_count);
        $this->assertSame(0, $challenge->clear_count);

        // チャレンジ中に同じステップに挑戦しようとすると403エラーレスポンスが返ってくるか
        $response = $this->actingAs($this->user)->postJson('/api/steps/challenges', ['id' => $step->id]);
        $response->assertForbidden();

        $challenge = ChallengeInformation::where('user_id', $this->user->id)->first();
        $challenge_step = ChallengeStep::where('challenge_user_id', $this->user->id)->first();

        // オリジナルステップ情報をコピーしたチャレンジステップ情報が作成されているか
        $this->assertSame($step->id, $challenge_step->step_id);
        // 子ステップ情報含めチャレンジ情報で作成されているか
        $this->assertSame(2, $challenge_step->challengeSubSteps->count());

        // 失敗に更新し、チャレンジ状況の情報が更新されているか
        $challenge_step->update(['status' => ChallengeStatusEnum::Failed]);
        $challenge->refresh();
        $this->assertSame(1, $challenge->challenge_count);
        $this->assertSame(0, $challenge->challenging_count);
        $this->assertSame(0, $challenge->clear_count);
        $this->assertSame(1, $challenge->fail_count);

        // 達成済に更新し、チャレンジ状況の情報が更新されているか
        $challenge_step->update(['status' => ChallengeStatusEnum::Cleared]);
        $challenge->refresh();
        $this->assertSame(1, $challenge->challenge_count);
        $this->assertSame(0, $challenge->challenging_count);
        $this->assertSame(1, $challenge->clear_count);
        $this->assertSame(0, $challenge->fail_count);
    }

    public function test_updateStep(): void
    {
        // 自分が作者のステップ情報(子ステップ2つ)を作成 (sort_numberを設定しつつ作成)
        $step = Step::factory()
            ->has(SubStep::factory()->count(2)->sequence(
                ['sort_number' => 1],
                ['sort_number' => 2],
            ))
            ->create([
                'user_id' => $this->user->id,
                'category_id' => $this->category->id,
                'achievement_time_type_id' => $this->achievement_time_type->id,
            ]);
        // 編集後の情報を設定
        $sub_step_count = 5;
        $sub_steps = [];
        for ($i=1; $i <= $sub_step_count; $i++) {
            $sub_steps[] = [
                'name' => '編集後のサブステップ' . $i,
                'detail' => '編集後のサブステップ詳細' . $i,
                'sort_number' => $i,
            ];
        }
        $category = Category::factory()->create();
        $achievement_time_type = AchievementTimeType::factory()->create();
        $params = [
            'id' => $step->id,
            'category_id' => $category->id,
            'achievement_time_type_id' => $achievement_time_type->id,
            'name' => '編集後のステップ名',
            'sub_steps' => $sub_steps,
        ];
        $response = $this->actingAs($this->user)->putJson('/api/steps/edit', $params);
        $response->assertOk();
        // 更新後の情報と比較
        $step->refresh();
        $this->assertSame($params['name'], $step->name);
        $this->assertSame($params['category_id'], $step->category_id);
        $this->assertSame($params['achievement_time_type_id'], $step->achievement_time_type_id);
        $this->assertSame($sub_step_count, $step->subSteps->count());
        foreach ($step->subSteps as $key => $sub_step) {
            $this->assertSame($params['sub_steps'][$key]['name'], $sub_step->name);
            $this->assertSame($params['sub_steps'][$key]['detail'], $sub_step->detail);
            $this->assertSame($params['sub_steps'][$key]['sort_number'], $sub_step->sort_number);
        }
    }

    public function test_deleteStep(): void
    {
        // 自分が作者のステップ情報(子ステップ2つ)を作成 (sort_numberを設定しつつ作成)
        $step = Step::factory()
            ->has(SubStep::factory()->count(2)->sequence(
                ['sort_number' => 1],
                ['sort_number' => 2],
            ))
            ->create([
                'user_id' => $this->user->id,
                'category_id' => $this->category->id,
                'achievement_time_type_id' => $this->achievement_time_type->id,
            ]);
        // ステップ削除
        $response = $this->actingAs($this->user)->deleteJson('/api/steps/delete', ['id' => $step->id]);
        $response->assertOk();
        // ステップが削除されているか
        $this->assertFalse(Step::where('id', $step->id)->exists());
        // 子ステップが削除されているか
        $this->assertFalse(SubStep::where('step_id', $step->id)->exists());
    }

    /**
     * chat GPT API completionテスト
     *
     * TODO: テストでAPI実行しても課金されるので、API処理モック化
     * @return void
     */
    public function test_completion(): void
    {
        // TODO: 詳細調べてmock化
        // $client = new ClientFake([
        //     CreateResponse::fake([
        //         'choices' => [
        //             [
        //                 'text' => 'awesome!',
        //             ],
        //         ],
        //     ]),
        // ]);

        $params = ['title' => '健康的にダイエット', 'prompt' => '朝食をしっかりとる']; // 12 token

        // NOTE: API mock 化できてないので、必要時のみコメントアウト解除
        // $response = $this->actingAs($this->user)->postJson('/api/chat-gpt/completion', $params);
        // $response->assertOk();
        // $this->assertArrayHasKey('message', $response);
        // $this->assertArrayHasKey('remain_count', $response);
        // 実行回数が1回になっているか
        // $chat_gpt_usage_information = $this->user->chatGptUsageInformations()->where('date', now()->format('Y-m-d'))->first()->refresh();
        // $this->assertSame(1, $chat_gpt_usage_information->usage_count);

        // 実行回数上限に設定
        $this->user->chatGptUsageInformations()->updateOrCreate(
            ['date' => now()->format('Y-m-d')],
            ['usage_count' => ChatGptUsageInformation::LIMIT_PER_DAY]
        );
        $response = $this->actingAs($this->user)->postJson('/api/chat-gpt/completion', $params);
        // 利用回数上限を警告するようになっているか
        $response->assertForbidden();
        // プロンプトが保存されているか
        $response->assertJson(['message' => __('messages.reached_prompt_limit')]);

        // TODO: mock処理のアサーション
        // OpenAI::assertSent(Completions::class, function (string $method, array $parameters): bool {
        //     return $method === 'create' &&
        //         $parameters['model'] === 'text-davinci-003' &&
        //         $parameters['prompt'] === 'PHP is ';
        // });
    }
}
