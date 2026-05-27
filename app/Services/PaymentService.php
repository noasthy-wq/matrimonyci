<?php

namespace App\Services;

use App\Models\Payment;

class PaymentService
{
    /**
     * Obtient l'instance du service de paiement pour un fournisseur
     */
    public function getProvider(string $provider)
    {
        return match ($provider) {
            'orange-money' => new OrangeMoneyService(),
            'mtn-money' => new MTNMoneyService(),
            'moov-money' => new MoovMoneyService(),
            'wave' => new WaveMoneyService(),
            default => throw new \InvalidArgumentException('Unknown payment provider: ' . $provider),
        };
    }

    /**
     * Initie un paiement
     */
    public function initiate(Payment $payment): array
    {
        $provider = $this->getProvider($payment->provider);
        return $provider->initiatePayment($payment);
    }

    /**
     * Vérifie le statut d'un paiement
     */
    public function checkStatus(Payment $payment): array
    {
        if (!$payment->transaction_id) {
            return [
                'success' => false,
                'error' => 'No transaction ID found',
            ];
        }

        $provider = $this->getProvider($payment->provider);
        return $provider->checkPaymentStatus($payment->transaction_id);
    }
}
