<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ChallengeStatusEnum;
use App\Models\AchievementTimeType;
use App\Models\Category;
use App\Models\ChallengeInformation;
use App\Models\ChallengeStep;
use App\Models\ChallengeSubStep;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ChallengeStepControllerTest extends TestCase
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
    public function test_notAllowLoginUserAccess(): void
    {
        // ログイン状態でないとチャレンジ中のステップ情報が見れないようになっているか
        $response = $this->getJson('/api/challenge-steps/1');
        $response->assertUnauthorized();
        // ログイン状態で存在しないチャレンジ情報を見ても422エラーになるか
        $response = $this->actingAs($this->user)->getJson('/api/challenge-steps/4649');
        $response->assertUnprocessable();
    }

    public function test_challengeStepShow(): void
    {
        // ステップとそれに紐づくサブステップを3つ作成
        $step = Step::factory()
            ->has(SubStep::factory()->count(3))
            ->create([
            'user_id' => User::factory()->create()->id,
            'category_id' => $this->category->id,
            'achievement_time_type_id' => $this->achievement_time_type->id,
        ]);

        // 作成したステップに紐づくチャレンジステップ情報作成
        $challenge_step = Event::fakeFor(function() use ($step) {
                    return ChallengeStep::factory()
                    ->has(ChallengeSubStep::factory()->count(3)->sequence(
                        ['sub_step_id' => $step->subSteps()->first()->id, 'status' => ChallengeStatusEnum::Challenging],
                        [ 'sub_step_id' => $step->subSteps()->first()->id, 'status' => ChallengeStatusEnum::Challenging],
                        [ 'sub_step_id' => $step->subSteps()->first()->id, 'status' => ChallengeStatusEnum::Challenging],
                    ))
                    ->create([
                    'step_id' => $step->id,
                    'challenge_user_id' => $this->user->id,
                    'category_id' => $this->category->id,
                    'achievement_time_type_id' => $this->achievement_time_type->id,
                    'post_user_id' => $step->user_id,
                    'status' => ChallengeStatusEnum::Challenging,
                    'challenge_user_id' => $this->user->id,
                ]);
            });


        $response = $this->actingAs($this->user)->getJson('/api/challenge-steps/' . $challenge_step->id);

        // $response->dump();
        $response->assertJson([
            'id' => $challenge_step->id,
            'post_user_id' => $step->user_id,
            'challenge_user_id' => $this->user->id,
            'cleared_sub_steps_count' => 0,
        ]);

        // 1つでもクリア済みに更新しクリア数をチェック
        $challenge_step->challengeSubSteps()->first()->update(['status' => ChallengeStatusEnum::Cleared]);
        $response = $this->actingAs($this->user)->getJson('/api/challenge-steps/' . $challenge_step->id);
        $response->assertJson([
            'id' => $challenge_step->id,
            'post_user_id' => $step->user_id,
            'challenge_user_id' => $this->user->id,
            'cleared_sub_steps_count' => 1,
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

        // クリアAPI実行し、チャレンジ状況(サブステップ)が更新されているか
        $response = $this->actingAs($this->user)->putJson('/api/challenge-steps/clear', [
            'challenge_step_id' => $challenge_step->id,
            'challenge_sub_step_id' => $challenge_step->challengeSubSteps[0]->id,
        ]);
        $response->dump();
        $response->assertOk();

        // 達成済に更新し、チャレンジ状況の情報が更新されているか
        $challenge_step->update(['status' => ChallengeStatusEnum::Cleared]);
        $challenge->refresh();
        $this->assertSame(1, $challenge->challenge_count);
        $this->assertSame(0, $challenge->challenging_count);
        $this->assertSame(1, $challenge->clear_count);
        $this->assertSame(0, $challenge->fail_count);


    }
}
