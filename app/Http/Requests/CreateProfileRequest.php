<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProfileRequest extends FormRequest
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
            'gender' => 'required|in:homme,femme,autre',
            'age' => 'required|integer|min:18|max:100',
            'religion' => 'nullable|string|max:50',
            'profession' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
            'city' => 'required|string|max:100',
            'education' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:50',
            'height' => 'nullable|integer|min:100|max:250',
            'complexion' => 'nullable|string|max:50',
            'looking_for' => 'nullable|string|max:500',
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
            'gender.required' => 'Le genre est requis',
            'age.required' => 'L\'âge est requis',
            'age.min' => 'Vous devez avoir au moins 18 ans',
            'city.required' => 'La ville est requise',
        ];
    }
}
