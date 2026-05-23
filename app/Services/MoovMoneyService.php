<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;

class MoovMoneyService
{
    private $apiUrl;
    private $apiKey;
    private $accountId;

    public function __construct()
    {
        $this->apiUrl = config('services.moov_money.api_url');
        $this->apiKey = config('services.moov_money.api_key');
        $this->accountId = config('services.moov_money.account_id');
    }

    /**
     * Crée une demande de paiement
     */
    public function initiatePayment(Payment $payment): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->apiUrl . '/transfers', [
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'source' => [
                    'accountId' => $this->accountId,
                ],
                'destination' => [
                    'phone' => $payment->phone_number,
                ],
                'description' => 'MatrimonyCI Subscription',
                'metadata' => [
                    'reference' => $payment->reference,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $payment->update([
                    'transaction_id' => $data['id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'transaction_id' => $data['id'] ?? null,
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
            ])->get($this->apiUrl . '/transfers/' . $transactionId);

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
