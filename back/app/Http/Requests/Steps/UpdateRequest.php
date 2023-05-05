<?php declare(strict_types=1);

namespace App\Http\Requests\Steps;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'name' => ['required', 'max:255'],
            'summary' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'achievement_time_type_id' => ['required', 'exists:achievement_time_types,id'],
            'sub_steps' => ['array', 'min:1'],
            'sub_steps.*.name' => ['required', 'string', 'max:255'],
            'sub_steps.*.detail' => ['required', 'string',],
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'ステップ名',
            'category_id' => 'カテゴリー',
            'achievement_time_type_id' => '達成時間タイプ',
            'sub_steps' => '子ステップ',
            'sub_steps.*.name' => '子ステップ名',
            'sub_steps.*.detail' => '子ステップ詳細',
        ];
    }
}
