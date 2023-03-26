<?php declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\StepService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MypageServiceTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;
    private  $step_service;

    /**
     * テストユーザー作成処理
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->step_service = app(StepService::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }


    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        // 登録したい入力データを設定
        $params = [
            'name' => 'ステップ情報',
            'category_id' => 1,
            'achievement_date_count' => 1,
            'achievement_date_time' => '12:34:56',
        ];
        // メソッドへ渡す
        $result = $this->step_service->store($params);
        // 登録されたステップ情報が返ってきているか
        // 期待値との比較
        $this->assertSame($params, $result->toArray());
    }
}
