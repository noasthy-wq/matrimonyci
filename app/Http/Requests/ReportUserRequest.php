<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportUserRequest extends FormRequest
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
            'reported_user_id' => 'required|exists:users,id',
            'reason' => 'required|in:fraud,harassment,inappropriate-content,spam,fake-profile',
            'description' => 'nullable|string|max:1000',
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
            'reported_user_id.required' => 'L\'utilisateur à signaler est requis',
            'reported_user_id.exists' => 'L\'utilisateur n\'existe pas',
            'reason.required' => 'La raison du signalement est requise',
            'reason.in' => 'La raison du signalement est invalide',
        ];
    }
}
