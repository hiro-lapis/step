<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ユーザーログイン状態確認
Route::get('is-login', function () {
    Mail::send('emails.sample', [], function ($message) {
        $message->to('hoge@example.com', 'Test')->subject('test mail');
    });
    return response()->json(['is_login' => auth()->user() !== null, 'user' => auth()->user()]);
});
// Route::get('is-login', fn () => response()->json(['is_login' => auth()->user() !== null, 'user' => auth()->user()]));

// 認証
Route::middleware('guest')->group(function () {
    $limiter = config('fortify.limiters.login');
    // ログイン
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware(array_filter([$limiter ? 'throttle:'.$limiter : null]))->name('api.login');
});
// ログアウト
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
