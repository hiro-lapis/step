<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AchievementTimeType;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MyPageControllerTest extends TestCase
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
    public function test_notAllowGuestUserAccess(): void
    {
        // 未ログイン状態でアクセス
        $response = $this->getJson('/api/mypage');
        $response->assertUnauthorized();
        // ログイン状態でアクセス
        $response = $this->actingAs($this->user)->getJson('/api/mypage');
        $response->assertOk();
    }

    public function test_ProfileIndex(): void
    {
        // ログイン状態でアクセス
        $response = $this->actingAs($this->user)->getJson('/api/mypage');
        $response->assertOk();

        // パスワード以外の情報を取得できているか
        $response->assertJson([
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'profile' => $this->user->profile,
                'image_url' => $this->user->image_url,
            ],
        ]);
        // JSON文字列をstdClassへデコードし、さらに配列へ変換
        $result = (array)json_decode($response->content());
        $this->assertArrayNotHasKey('password', $result);
    }

    public function test_updateProfile(): void
    {
        User::factory()->create(['email' => 'foo@bar.com']);
        $params = [
            'name' => 'foo',
            'email' => 'foo@bar.com',
        ];
        $response = $this->actingAs($this->user)->postJson('/api/mypage/profile', $params);
        // メールアドレス重複の422エラーを確認
        $response->assertJson([
            'errors' => [
                'email' => ['メールアドレスは既に存在します'],
            ],
        ]);
        $response->assertUnprocessable();
        $params = [
            'name' => 'foo',
            'email' => 'hoge@bar.com',
        ];
        $response = $this->actingAs($this->user)->postJson('/api/mypage/profile', $params);
        $response->assertOk();
        // 最新状態を取得
        $this->user->refresh();
        // APIで更新したプロフィール情報になっているか
        $this->assertSame($params['name'], $this->user->name);
        $this->assertSame($params['email'], $this->user->email);
    }

    public function test_updateProfileImage(): void
    {
        // アップロード処理をスタブ
        Storage::fake('s3');
        $file = UploadedFile::fake()->image('test_user_account.png');
        $params = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'file' => $file,
        ];
        // アカウント画像ファイルのアップロード
        $response = $this->actingAs($this->user)->postJson('/api/mypage/profile', $params);

        // 表示のためのメッセージ確認
        $response->assertJson([
            'message' => 'プロフィール情報を更新しました',
            'user' => [
                'name' => $params['name'],
                'email' => $params['email'],
            ],
        ]);
        // ファイルパス情報が更新されているか
        $before_path = $this->user->image_url;
        $this->user->refresh();
        $this->assertNotSame($before_path, $this->user->image_url);
    }

    public function test_updatePassword(): void
    {
        $current_password = 'password';
        $user = User::factory()->create(['password' => Hash::make($current_password)]);
        $params = [
            'current_password' => $current_password,
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ];
        $response = $this->actingAs($user)->putJson('/api/mypage/password', $params);
        $response->assertOk();
        $user->refresh();
        // 更新後のハッシュ化パスワードとの整合性をチェック
        $this->assertTrue(Hash::check($params['password'], $user->password));
    }
}
