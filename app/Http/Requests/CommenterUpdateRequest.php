<?php

namespace App\Http\Requests;

use App\Enums\Roles;
use Illuminate\Foundation\Http\FormRequest;

class CommenterUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole([
            Roles::ADMIN->value,
            Roles::COMMENTER->value
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|min:3',
            'email' => 'nullable|email',
            'email_verified_at' => 'nullable|date',
            'avatar_url' => 'nullable|url',
            'bio' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Name hanya boleh huruf',
            'email.email' => 'Email tidak valid',
            'email_verified_at.date' => 'Email tidak valid',
            'avatar_url.url' => 'URL tidak boleh kosong',
            'bio' => 'Bio tidak boleh kosong',
        ];
    }
}
