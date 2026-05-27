<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;

class WaveMoneyService
{
    private $apiUrl;
    private $apiKey;
    private $merchantId;

    public function __construct()
    {
        $this->apiUrl = config('services.wave.api_url');
        $this->apiKey = config('services.wave.api_key');
        $this->merchantId = config('services.wave.merchant_id');
    }

    /**
     * Crée une demande de paiement
     */
    public function initiatePayment(Payment $payment): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->apiUrl . '/v1/checkout/create', [
                'merchant_id' => $this->merchantId,
                'amount' => (int)($payment->amount * 100), // Convert to cents
                'currency' => $payment->currency,
                'phone_number' => $payment->phone_number,
                'description' => 'MatrimonyCI Subscription',
                'reference' => $payment->reference,
                'callback_url' => route('api.payment.callback'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $payment->update([
                    'transaction_id' => $data['checkout_id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'transaction_id' => $data['checkout_id'] ?? null,
                    'checkout_url' => $data['checkout_url'] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'Payment initiation failed',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Vérifie le statut du paiement
     */
    public function checkPaymentStatus($transactionId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->apiUrl . '/v1/checkout/' . $transactionId);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $response->json()['status'] ?? 'unknown',
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to check payment status',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
