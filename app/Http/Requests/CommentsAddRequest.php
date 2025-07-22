<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentsAddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasPermissionTo('create comments');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
            'tenant_id' => 'required|exists:tenants,id',
            'article_id' => 'required|string',
            'article_url' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User id tidak boleh kosong',
            'user_id.exists' => 'User tidak ditemukan',
            'content.required' => 'Content tidak boleh kosong.',
            'content.string' => 'Content harus berupa string.',
            'parent_id.exists' => 'Parent komentar tidak ditemukan.',
            'tenant_id.required' => 'Tenant id tidak boleh kosong.',
            'tenant_id.exists' => 'Tenant tidak ditemukan',
            'article_id.required' => 'Article id tidak boleh kosong.',
            'article_url.required' => 'Article url tidak boleh kosong.',
        ];
    }
}
