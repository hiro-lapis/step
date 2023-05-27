<?php declare(strict_types=1);

namespace App\Http\Requests\Steps;

use Illuminate\Validation\Rule;
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
        $default_rule = [
            'id' => ['required', 'exists:steps,id'],
            'name' => ['required', 'max:255'],
            'image_url' => ['nullable', 'string'],
            'summary' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'achievement_time_type_id' => ['required', 'exists:achievement_time_types,id'],
            'time_count' => ['required', 'min:1'],
            'sub_steps' => ['array', 'min:1'],
            'sub_steps.*.name' => ['nullable', 'string', 'max:255'],
            'sub_steps.*.detail' => ['nullable', 'string', 'max:2000'],
        ];

        $achievement_time_type_id = $this->input('achievement_time_type_id');
        $default_rule['time_count'] = CreateRequest::getAchievementTimtTypeMaxRule($achievement_time_type_id);
        return $default_rule;
    }

    public function attributes()
    {
        return [
            'name' => 'ステップ名',
            'category_id' => 'カテゴリー',
            'image_url' => 'ステップアイキャッチ画像',
            'achievement_time_type_id' => '達成時間単位',
            'time_count' => '達成時間',
            'sub_steps' => 'サブステップ',
            'sub_steps.*.name' => 'サブステップ名',
            'sub_steps.*.detail' => 'サブステップ詳細',
        ];
    }

    public function messages()
    {
        $achievement_time_type_id = $this->input('achievement_time_type_id');

        return [
            'sub_steps.min' => 'サブステップを1つ以上登録してください',
            'time_count.min' => ':attributeは1以上の値を入力してください',
        ] + CreateRequest::getAchievementTimtTypeMaxMessage($achievement_time_type_id);
    }
}
