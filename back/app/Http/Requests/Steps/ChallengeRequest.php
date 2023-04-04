<?php declare(strict_types=1);

namespace App\Http\Requests\Steps;

use Illuminate\Foundation\Http\FormRequest;

class ChallengeRequest extends FormRequest
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
            'id' => ['required', 'exists:steps,id'],
            // TODO：バリデーション
            // ステップ作成者自身でないか
            // 現在すでに対象のステップにチャレンジ中でないか

            //  NOTE:期限付きにするかどうか
        ];
    }

    /**
     * バリデーション前に実行される処理
     * ルートパラメータを入力値に追加してバリデーション
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // パラメータはstring型なのでキャスト
        return $this->merge([
            'user_id' => auth()->user()->id ?? 0,
        ]);
    }
}
