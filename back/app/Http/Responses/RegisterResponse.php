<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

/**
 * RegisterResponse
 * 登録時ユーザーの認証情報を返すレスポンスクラス
 * Fortifyのクラスをカスタマイズ。ユーザー情報を返却する
 * vendor/laravel/fortify/src/Http/Responses/RegisterResponse.php
 */
class RegisterResponse implements RegisterResponseContract
{
    /**
     *
     * Orignal: vendor/laravel/fortify/src/Http/Responses/RegisterResponse.php
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $user = auth()->user();
        // create時はimageのアクセサ効かせるためfresh
        return $request->wantsJson()
                    ? response()->json(compact('user'))
                    : redirect()->intended(config('fortify.home'));
    }
}
