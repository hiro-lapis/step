<?php declare(strict_types=1);

namespace App\Http\Requests\Mypage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'current_password' => ['required', 'current_password:web'],
            // 文字・数字をそれぞれ1文字以上含む8文字以上のパスワード
            'password' => ['bail','required', 'confirmed', 'different:current_password', 'between:8,24', Password::default()->letters()],
            'password_confirmation' => [],
        ];
    }
}
