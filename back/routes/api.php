<?php

use App\Http\Controllers\Api\AchievementTimeTypeController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\StepController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
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
    // ステップ新規作成
    Route::post('/steps', [StepController::class, 'store'])->name('steps.store');
});
// ステップ一覧
Route::get('/steps', [StepController::class, 'index'])->name('steps.index');

// ユーザーログイン状態確認
Route::get('is-login', fn () => response()->json(['is_login' => auth()->user() !== null, 'user' => auth()->user()]));

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
// ログアウト
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
