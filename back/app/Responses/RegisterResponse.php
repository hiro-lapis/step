<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

/**
 * 登録時ユーザーの認証情報を返すレスポンスクラス
 */
class RegisterResponse implements LoginResponseContract
{
    /**
     *
     * Orignal: vendor/laravel/fortify/src/Http/Responses/LoginResponse.php
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $user = auth()->user();

        return $request->wantsJson()
                    ? response()->json(compact('user'))
                    : redirect()->intended(config('fortify.home'));
    }
}
