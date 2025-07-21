<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommenterRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'bio' => 'nullable|string',
            'avatar_url' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name tidak boleh kosong',
            'name.unique' => 'Name sudah terdaftar',
            'name.string' => 'Name hanya boleh huruf',
            'email.required' => 'Email tidak boleh kosong',
            'email.unique' => 'Email sudah terdaftar',
            'email.email' => 'Email tidak valid',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password harus lebih dari 8 karakter',
            'password.string' => 'Password hanya boleh huruf',
            'avatar_url.string' => 'Avatar URL hanya boleh huruf',
            'bio.string' => 'Bio hanya boleh huruf',
        ];
    }
}
