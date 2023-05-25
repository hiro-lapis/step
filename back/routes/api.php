<?php

use App\Http\Controllers\Api\AchievementTimeTypeController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ChallengeStepController;
use App\Http\Controllers\Api\MypageController;
use App\Http\Controllers\Api\PresignedUploadUrlController;
use App\Http\Controllers\Api\StepController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', fn(Request $request) => $request->user());
    // ステップAPIサジェスト
    Route::post('/chat-gpt/completion', [StepController::class, 'completion'])->name('steps.completion');
    // ステップ新規作成
    Route::post('/steps', [StepController::class, 'store'])->name('steps.store');
    Route::post('/steps/draft', [StepController::class, 'storeDraft'])->name('steps.storeDraft');
    Route::post('/steps/challenges', [StepController::class, 'challenge'])->name('steps.challenge');
    // ステップ更新
    Route::put('/steps/update', [StepController::class, 'update'])->name('steps.update');
    // ステップ編集情報取得
    Route::get('/steps/{id}/edit', [StepController::class, 'edit'])->name('steps.edit');
    // ステップ削除
    Route::delete('/steps/delete', [StepController::class, 'destroy'])->name('steps.destroy');
    // チャレンジ中のステップ情報
    Route::group(['prefix' => 'challenge-steps'], function() {
        Route::get('{step_id}', [ChallengeStepController::class, 'show'])->name('.show');
        Route::put('/clear', [ChallengeStepController::class, 'clear'])->name('.clear');
    });
    Route::group(['prefix' => 'mypage'], function() {
        Route::get('', [MypageController::class, 'index'])->name('mypage.index');
        Route::post('/profile', [MypageController::class, 'updateProfile'])->name('mypage.update.profile');
        Route::get('/posted-step', [MypageController::class, 'postedStep'])->name('mypage.postedStep');
        Route::get('/challenging-step', [MypageController::class, 'challengingStep'])->name('mypage.challengingStep');
        Route::put('/password', [MypageController::class, 'updatePassword'])->name('mypage.update.password');
    });
    // アップロード署名付きURL取得
    Route::post('/presigned-upload-url', [PresignedUploadUrlController::class, 'presignedUploadUrl'])->name('presignedUploadUrl');
    Route::get('/presigned-download-url', [PresignedUploadUrlController::class, 'presignedDownloadUrl'])->name('presignedDownloadUrl');
    // ログアウト
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
// ステップ一覧
Route::get('/steps', [StepController::class, 'index'])->name('steps.index');
// ステップ詳細(兼編集情報取得)
Route::get('/steps/{id}', [StepController::class, 'show'])->name('steps.show');

// ユーザーログイン状態確認
Route::get('is-login', [MypageController::class, 'isLogin'])->name('mypage.isLogin');

// カテゴリー取得
Route::get('/categories', CategoryController::class);
Route::get('/achievement-time-types', AchievementTimeTypeController::class);

// 認証
Route::middleware('guest')->group(function () {
    $limiter = config('fortify.limiters.login');
    // ユーザー登録
    Route::post('/register', [RegisteredUserController::class, 'store']);
    // ログイン
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware(array_filter([$limiter ? 'throttle:'.$limiter : null]))->name('api.login');
    // パスワードリセット手続メール配信
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.forgot');
    // メール配信後のリセット
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.reset');
});
