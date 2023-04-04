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
        return [
            'name' => ['required', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'achievement_time_type_id' => ['required', 'exists:achievement_time_types,id'],
            'sub_steps' => ['array', 'min:1'],
            'sub_steps.*' => ['array:name,detail,sort_number'],
        ];
    }
}
