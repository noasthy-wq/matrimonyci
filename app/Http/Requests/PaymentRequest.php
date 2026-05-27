<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class PaymentRequest extends FormRequest
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
            'subscription_tier' => 'required|in:premium_monthly,premium_annual',
            'provider' => 'required|in:orange-money,mtn-money,moov-money,wave',
            'phone_number' => 'required|string|regex:/^\+?[1-9]\d{1,14}$/',
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
            'subscription_tier.required' => 'Le type d\'abonnement est requis',
            'subscription_tier.in' => 'Le type d\'abonnement est invalide',
            'provider.required' => 'Le fournisseur de paiement est requis',
            'provider.in' => 'Le fournisseur de paiement est invalide',
            'phone_number.required' => 'Le numéro de téléphone est requis',
            'phone_number.regex' => 'Le numéro de téléphone n\'est pas valide',
        ];
    }
}
