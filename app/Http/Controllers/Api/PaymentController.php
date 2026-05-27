<?php

namespace App\Http\Controllers\Api;

use App\Models\Payment;
use App\Models\Subscription;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Crée une demande de paiement pour un abonnement
     */
    public function createPayment(Request $request)
    {
        $validated = $request->validate([
            'subscription_tier' => 'required|in:premium_monthly,premium_annual',
            'provider' => 'required|in:orange-money,mtn-money,moov-money,wave',
            'phone_number' => 'required|string',
        ]);

        $user = $request->user();
        $tier = $validated['subscription_tier'];
        $tierConfig = config('matrimony.subscriptions.tiers.' . $tier);

        // Créer un enregistrement de paiement
        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $tierConfig['price'],
            'currency' => $tierConfig['currency'],
            'provider' => $validated['provider'],
            'phone_number' => $validated['phone_number'],
            'reference' => 'REF-' . strtoupper(uniqid()),
            'status' => 'pending',
        ]);

        // Initier le paiement
        $result = $this->paymentService->initiate($payment);

        if (!$result['success']) {
            $payment->update(['status' => 'failed']);
            return response()->json([
                'message' => 'Payment initiation failed',
                'error' => $result['error'],
            ], 400);
        }

        return response()->json([
            'message' => 'Payment initiated successfully',
            'payment' => $payment,
            'transaction_id' => $result['transaction_id'] ?? null,
        ], 201);
    }

    /**
     * Vérifie le statut du paiement
     */
    public function checkPaymentStatus(Request $request, $paymentId)
    {
        $payment = Payment::find($paymentId);

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        if ($payment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $result = $this->paymentService->checkStatus($payment);

        if ($result['success'] && $result['status'] === 'completed') {
            // Marquer le paiement comme completé
            $payment->markAsPaid();

            // Créer ou mettre à jour la souscription
            $tierConfig = config('matrimony.subscriptions.tiers.' . $request->input('subscription_tier', 'free'));
            $subscription = Subscription::create([
                'user_id' => $payment->user_id,
                'tier' => $request->input('subscription_tier', 'free'),
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => now()->addDays($tierConfig['duration_days']),
            ]);

            $payment->update(['subscription_id' => $subscription->id]);
        }

        return response()->json([
            'message' => 'Payment status checked',
            'payment' => $payment,
            'status' => $result['status'] ?? 'unknown',
        ], 200);
    }

    /**
     * Webhook pour les notifications de paiement
     */
    public function webhook(Request $request)
    {
        $provider = $request->input('provider');
        $transactionId = $request->input('transaction_id');
        $status = $request->input('status');

        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        if ($status === 'completed') {
            $payment->markAsPaid();
        } elseif ($status === 'failed') {
            $payment->markAsFailed($request->input('error_message'));
        }

        return response()->json(['message' => 'Webhook processed'], 200);
    }
}
