<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCommentRequest extends FormRequest
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
            'profile_id' => 'required|exists:profiles,id',
            'content' => 'required|string|min:3|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'profile_id.required' => 'Le profil est requis',
            'profile_id.exists' => 'Le profil n\'existe pas',
            'content.required' => 'Le commentaire est requis',
            'content.min' => 'Le commentaire doit contenir au moins 3 caractères',
            'content.max' => 'Le commentaire ne peut pas dépasser 500 caractères',
        ];
    }
}
