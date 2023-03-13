<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AchievementTimeType;
use App\Models\Category;
use App\Models\Step;
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
        $params = [
            'name' => 'テストだよ',
            'category_id' => $this->category->id,
            'achievement_time_type_id' => $this->achievement_time_type->id,
        ];
        $this->actingAs($this->user)->postJson('/api/steps', $params);

        // 期待値の評価
        $this->assertDatabaseCount('steps', 1);
        $result = Step::first();
        $this->assertSame($params['name'], $result->name);
        $this->assertSame($this->user->id, $result->user_id);
        $this->assertSame($this->category->id, $result->category_id);
        $this->assertSame($params['achievement_time_type_id'], $result->achievement_time_type_id);
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
}
