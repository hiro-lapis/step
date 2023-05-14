<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AchievementTimeType;
use App\Models\Category;
use App\Models\ChallengeStep;
use App\Models\ChallengeSubStep;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class MyPageControllerTest extends TestCase
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
            'skip_api_confirm' => 'hoge',
        ];
        $response = $this->actingAs($this->user)->postJson('/api/mypage/profile', $params);
        // メールアドレス重複の422エラーを確認
        $response->assertJson([
            'errors' => [
                'email' => ['メールアドレスは既に存在します'],
                'skip_api_confirm' => ['API利用確認スキップ設定の値は true もしくは false のみ有効です'],

            ],
        ]);
        $response->assertUnprocessable();
        $params = [
            'name' => 'foo',
            'email' => 'hoge@bar.com',
            'profile' => 'テストプロフィール',
            'skip_api_confirm' => '1',
        ];
        $response = $this->actingAs($this->user)->postJson('/api/mypage/profile', $params);
        $response->assertOk();
        // 最新状態を取得
        $this->user->refresh();
        // APIで更新したプロフィール情報になっているか
        $this->assertSame($params['name'], $this->user->name);
        $this->assertSame($params['email'], $this->user->email);
        $this->assertSame($params['profile'], $this->user->profile);
        $this->assertSame(true, $this->user->skip_api_confirm);
    }

    public function test_updateProfileImage(): void
    {
        // アップロード処理をスタブ
        Storage::fake('s3');
        $file = UploadedFile::fake()->image('test_user_account.png');
        $update_profile = fake()->text();
        $params = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'file' => $file,
            'prifile' => $update_profile = fake()->text(),
            'skip_api_confirm' => '1' // フロントの実装に合わせて文字列で送信
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
        $new_password = 'new_password';
        $user = User::factory()->create(['password' => Hash::make($current_password)]);
        $params = [
            'current_password' => $current_password,
            'password' => $new_password,
            'password_confirmation' => $new_password,
        ];
        $response = $this->actingAs($user)->putJson('/api/mypage/password', $params);
        $response->assertOk();
        $user->refresh();

        // 更新後のハッシュ化パスワードとの整合性をチェック
        $this->assertTrue(Hash::check($params['password'], $user->password));

                /**
         * 以下バリデーションエラーケースのテスト
         */
        // 同じパスワードで更新しようとした時に422エラーを返すか
        $params = [
            'current_password' => $new_password,
            'password' => $new_password,
            'password_confirmation' => $new_password,
        ];
        $response = $this->actingAs($user)->putJson('/api/mypage/password', $params);
        $response->assertUnprocessable();
        $response->assertJson(fn (AssertableJson $json) =>
            $json->where('message', '現在のパスワードと同じパスワードは設定できません')
                ->etc() // 他の項目はチェックしない
        );
        // 7文字のパスワードで422エラーを返すか
        $params = [
            'current_password' => $new_password,
            'password' => 'abcdefg',
            'password_confirmation' => 'abcdefg',
        ];
        $response = $this->actingAs($user)->putJson('/api/mypage/password', $params);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->where('message', 'パスワードは8~24文字で設定してください')
                ->etc() // 他の項目はチェックしない
        );
        // パスワードで422エラーを返すか
        $params = [
            'current_password' => $new_password,
            'password' => 'abcdefghijklmnopqrstuvwxy',
            'password_confirmation' => 'abcdefghijklmnopqrstuvwxy',
        ];
        $response = $this->actingAs($user)->putJson('/api/mypage/password', $params);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->where('message', 'パスワードは8~24文字で設定してください')
                ->etc() // 他の項目はチェックしない
        );
        // 数字のみのパスワードで422エラーを返すか
        $params = [
            'current_password' => $new_password,
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];
        $response = $this->actingAs($user)->putJson('/api/mypage/password', $params);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->where('message', 'パスワード は文字を1文字以上含むのが必須です')
                ->etc() // 他の項目はチェックしない
        );
        $response->assertUnprocessable();
    }

    public function test_postedStep(): void
    {
        $steps = Step::factory(5)
            ->has(SubStep::factory()->count(2)->sequence(
                ['sort_number' => 1],
                ['sort_number' => 2],
            ))
            ->create([
                'user_id' => $this->user->id,
                'category_id' => $this->category->id,
                'achievement_time_type_id' => $this->achievement_time_type->id,
            ]);
        $response = $this->actingAs($this->user)->getJson('/api/mypage/posted-step');
        $response->assertOk();

        $this->assertSame(5, count($response->json('steps')));
        $this->assertSame($response->json('steps')[0]['id'], $steps[0]->id);
    }

    public function test_challengingStep(): void
    {

        $steps = ChallengeStep::factory(5)
            ->has(ChallengeSubStep::factory()->count(2)->sequence(
                ['sub_step_id' => 1, 'sort_number' => 1],
                ['sub_step_id' => 2, 'sort_number' => 2],
            ))
            ->create([
                'challenge_user_id' => $this->user->id, // ログインユーザーのチャレンジ情報として作成
                'challenged_at' => now()->subDays(7),
                'category_id' => $this->category->id,
                'achievement_time_type_id' => $this->achievement_time_type->id,
                'step_id' => 1,
                'post_user_id' => 1,
            ]);
        $response = $this->actingAs($this->user)->getJson('/api/mypage/challenging-step');
        $response->assertOk();
        $this->assertSame(5, count($response->json('steps')));
        $this->assertSame($response->json('steps')[0]['id'], $steps[0]->id);
    }

    public function test_isLogin(): void
    {
        $response = $this->getJson('/api/is-login');
        $response->assertOk();
        // 未ログイン状態の情報が返っているか
        $response->assertJson([
            'user' => null,
            'step_ids' => [],
            'is_login' => false,
            'remain_count' => 0,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/is-login');
        $response->assertOk();
        // ログイン状態の情報が返っているか
        $response->assertJson([
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'image_url' => $this->user->image_url,
                'profile' => $this->user->profile,
            ],
            'step_ids' => [],
            'is_login' => true,
            'remain_count' => 100,
        ]);
    }
}
