<?php declare(strict_types=1);

namespace App\Http\Requests\Steps;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexRequest extends FormRequest
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
            'key_word' => ['nullable'],
            'page' => ['nullable', 'integer'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'order_by' => ['nullable', 'string', Rule::in(['steps.created_at', 'sub_steps_count', 'achievement_time_types.sort_number'])],
            'desc' => ['nullable', 'boolean'],
        ];
    }
}
