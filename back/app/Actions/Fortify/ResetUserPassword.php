<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\ResetsUserPasswords;
use Symfony\Component\HttpFoundation\Response;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     */
    public function reset(User $user, array $input): void
    {
        $password = $input['password'];
        // 現在のパスワードと同じかチェック
        if (Hash::check($password, $user->password)) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, __('validation.custom.password.different'));
        }

        Validator::make($input, [
            // original
            // 'password' => $this->passwordRules(), // ['required', 'string', new Password, 'confirmed']
            // パスワード更新(MypageController@updatePassword())と同じルール
            'password' => ['bail','required', 'confirmed', 'between:8,24', Password::min(8)->letters()],
        ])->validate();

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
