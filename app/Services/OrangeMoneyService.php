<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;

class OrangeMoneyService
{
    private $apiUrl;
    private $clientId;
    private $clientSecret;
    private $merchantId;

    public function __construct()
    {
        $this->apiUrl = config('services.orange_money.api_url');
        $this->clientId = config('services.orange_money.client_id');
        $this->clientSecret = config('services.orange_money.client_secret');
        $this->merchantId = config('services.orange_money.merchant_id');
    }

    /**
     * Crée une demande de paiement
     */
    public function initiatePayment(Payment $payment): array
    {
        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->post($this->apiUrl . '/payment/request', [
                    'merchant_id' => $this->merchantId,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'phone_number' => $payment->phone_number,
                    'reference' => $payment->reference,
                    'description' => 'MatrimonyCI Subscription',
                    'callback_url' => route('api.payment.callback'),
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $payment->update([
                    'transaction_id' => $data['transaction_id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'transaction_id' => $data['transaction_id'] ?? null,
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
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->get($this->apiUrl . '/payment/status', [
                    'transaction_id' => $transactionId,
                ]);

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
