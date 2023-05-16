<?php
declare(strict_types=1);

namespace App\Http\Requests\PresignedUpload;

use Illuminate\Foundation\Http\FormRequest;

class GetPresignedDownloadUrlRequst extends FormRequest
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
            'file_path' => ['required', 'string', 'max:255',],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'file_path' => $this->file_path,
        ]);
    }
}
