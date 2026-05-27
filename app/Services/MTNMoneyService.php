<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;

class MTNMoneyService
{
    private $apiUrl;
    private $primaryKey;
    private $secondaryKey;
    private $userId;

    public function __construct()
    {
        $this->apiUrl = config('services.mtn_money.api_url');
        $this->primaryKey = config('services.mtn_money.primary_key');
        $this->secondaryKey = config('services.mtn_money.secondary_key');
        $this->userId = config('services.mtn_money.user_id');
    }

    /**
     * Crée une demande de paiement
     */
    public function initiatePayment(Payment $payment): array
    {
        try {
            $response = Http::withHeaders([
                'X-Target-Environment' => 'sandbox',
                'Ocp-Apim-Subscription-Key' => $this->primaryKey,
            ])->post($this->apiUrl . '/collection/v1_0/requesttopay', [
                'amount' => (string)$payment->amount,
                'currency' => $payment->currency,
                'externalId' => $payment->reference,
                'payer' => [
                    'partyIdType' => 'MSISDN',
                    'partyId' => $payment->phone_number,
                ],
                'payerMessage' => 'MatrimonyCI Subscription Payment',
                'payeeNote' => 'MatrimonyCI',
            ]);

            if ($response->successful()) {
                $transactionId = $response->header('X-Reference-Id');
                $payment->update([
                    'transaction_id' => $transactionId,
                ]);

                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
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
                'X-Target-Environment' => 'sandbox',
                'Ocp-Apim-Subscription-Key' => $this->primaryKey,
            ])->get($this->apiUrl . '/collection/v1_0/requesttopay/' . $transactionId);

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
