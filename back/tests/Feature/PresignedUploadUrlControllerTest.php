<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AchievementTimeType;
use App\Models\Category;
use App\Models\ChallengeStep;
use App\Models\ChallengeSubStep;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PresignedUploadUrlControllerTest extends TestCase
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
     * ログイン状態に応じたAPI利用のテスト
     *
     * @return void
     */
    public function test_notAllowGuestUserAccess(): void
    {
        // 未ログインユーザーが利用できないようになっているか
        $response = $this->postJson('/api/presigned-upload-url');
        $response->assertUnauthorized();

        // ログインユーザーが利用できるようになっているか(ファイル名がないため422エラー)
        $response = $this->actingAs($this->user)->postJson('/api/presigned-upload-url');
        $response->assertUnprocessable();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    // public function test_presignedUploadUrl(): void
    // {
    //     Storage::fake('s3');
    //     // ログインユーザーが利用できるようになっているか(ファイル名がないため422エラー)
    //     $response = $this->actingAs($this->user)->postJson('/api/presigned-upload-url', ['file_name' => 'test.jpg']);

    //     $response->assertOk();
    //     $data = $response->json();
    //     $this->assertArrayHasKey('upload_path', $data);
    //     $this->assertArrayHasKey('presigned_url', $data);
    // }
}
