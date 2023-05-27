<?php declare(strict_types=1);

namespace App\Http\Requests\Steps;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            'name' => ['required', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'image_url' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'achievement_time_type_id' => ['required', 'exists:achievement_time_types,id'],
            'time_count' => ['required', 'integer', 'min:1'],
            'sub_steps' => ['array',],
            'sub_steps.*.name' => ['nullable', 'string', 'max:255'],
            'sub_steps.*.detail' => ['nullable', 'string', 'max:2000'],
        ];

        $achievement_time_type_id = $this->input('achievement_time_type_id');
        $default_rule['time_count'] = self::getAchievementTimtTypeMaxRule($achievement_time_type_id);
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

    /**
     * 達成時間の最大値バリデーションルールを取得
     * 更新フォームリクエストと共用
     *
     * @param integer $achievement_time_type_id
     * @return array
     */
    public static function getAchievementTimtTypeMaxRule(int $achievement_time_type_id): array
    {
        $rule = ['required', 'integer', 'min:1'];
        switch ($achievement_time_type_id) {
            // 分
            case 1:
                $rule = ['required', 'integer', 'min:1', 'max:59'];
                break;
            // 時間
            case 2:
                $rule = ['required', 'integer', 'min:1', 'max:23'];
                break;
            // 日
            case 3:
                $rule = ['required', 'integer', 'min:1', 'max:30'];
                break;
            // 週間
            case 4:
                $rule = ['required', 'integer', 'min:1', 'max:4'];
                break;
            // 月
            case 5:
                $rule = ['required', 'integer', 'min:1', 'max:11'];
                break;
            // 年
            case 6:
                $rule = ['required', 'integer', 'min:1', 'max:10'];
                break;
            default:
                break;
        }
        return $rule;
    }

    /**
     * 達成時間の最大値バリデーションエラーメッセージを取得
     * 更新フォームリクエストと共用
     *
     * @param integer $achievement_time_type_id
     * @return string
     */
    public static function getAchievementTimtTypeMaxMessage(int $achievement_time_type_id): array
    {
        $max_message = '';
        switch ($achievement_time_type_id) {
            case 1:
                $max_message = "目安達成時間の単位が 分 なので59分以下を入力してください";
                break;
            case 2:
                $max_message = "目安達成時間の単位が 時間 なので23時間以下を入力してください";
                break;
            case 3:
                $max_message = "目安達成時間の単位が 日 なので30日以下を入力してください";
                break;
            case 4:
                $max_message = "目安達成時間の単位が 週間 なので4週間以下を入力してください";
                break;
            case 5:
                $max_message = "目安達成時間の単位が 月 なので11ヶ月以下を入力してください";
                break;
            case 6:
                $max_message = "目安達成時間の単位が 年 なので10年以下を入力してください";
                break;
            default:
                break;
        }
        return ['time_count.max' => $max_message];
    }

    public function messages()
    {
        $achievement_time_type_id = $this->input('achievement_time_type_id');
        return [
            'sub_steps.min' => 'サブステップを1つ以上登録してください',
            'time_count.min' => ':attributeは1以上の値を入力してください',
        ] + self::getAchievementTimtTypeMaxMessage($achievement_time_type_id);
    }
}
