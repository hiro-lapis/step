<?php declare(strict_types=1);

namespace App\Http\Requests\ChallengeSteps;

use Illuminate\Foundation\Http\FormRequest;

class ClearRequest extends FormRequest
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
            'id' => (int)$this->route('step_id')
        ]);
    }
}
