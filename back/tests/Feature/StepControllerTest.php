<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AchievementTimeType;
use App\Models\Category;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class StepControllerTest extends TestCase
{
    use DatabaseTransactions;

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

    public function test_storeCoorectStepData(): void
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
            'sub_steps' => $sub_steps,
        ];
        $this->actingAs($this->user)->postJson('/api/steps', $params);

        // 期待値の評価
        $this->assertDatabaseCount('steps', 1);
        $result = Step::first();
        $this->assertSame($params['name'], $result->name);
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
            ->hasStep(5, [
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

        // \Log::info('HIRO:responseの中身' . print_r(($response), true));
        // $response->dump();
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
        // 自分が作者のステップ情報を作成
        $step = Step::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'achievement_time_type_id' => $this->achievement_time_type->id,
        ]);

        // チャレンジ実行
        $response = $this->postJson('/api/steps/challenges');
        // 未ログインだと401になるか
        $response->assertUnauthorized();

        // ログイン状態でチャレンジ実行
        $response = $this->actingAs($this->user)->postJson('/api/steps/challenges');
        // 自作のステップには挑戦できない422レスポンスのメッセージが返ってくるか
        $response->assertUnprocessable();

    }
}
