<?php declare(strict_types=1);

namespace App\Http\Requests\Mypage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'name' => ['bail', 'required', 'string', 'max:255'],
            // ユーザーIDを除外条件に使ったユニークチェック
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user()->id)],
            // 画像ファイル(10mb迄)
            'file' => ['sometimes', 'max:10240', 'mimes:jpg,bmp,png'],
            'profile' => ['nullable', 'string'],
            'skip_api_confirm' => ['required', 'boolean'],
        ];
    }
}
