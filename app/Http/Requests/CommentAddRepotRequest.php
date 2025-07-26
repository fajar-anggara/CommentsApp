<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentAddRepotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasPermissionTo('reporting comments');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reason' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'reason.string' => 'Reason harus berupa string',
        ];
    }
}
