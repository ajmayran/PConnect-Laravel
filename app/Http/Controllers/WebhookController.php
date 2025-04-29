<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DistributorSubscription;
use App\Services\PaymongoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $paymongoService;

    public function __construct(PaymongoService $paymongoService)
    {
        $this->paymongoService = $paymongoService;
    }

    public function handlePaymongoWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Paymongo-Signature');

        Log::info('Webhook received', ['payload' => $payload, 'signature' => $sigHeader]);

        if (!$this->paymongoService->verifyWebhookSignature($payload, $sigHeader)) {
            Log::warning('Invalid Paymongo webhook signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = json_decode($payload, true);
        Log::info('Webhook payload decoded', ['data' => $data]);

        switch ($data['type']) {
            case 'checkout.session.completed':
                Log::info('Processing checkout.session.completed event');
                $this->handleCheckoutCompleted($data['data']);
                break;
            case 'payment.paid':
                Log::info('Processing payment.paid event');
                $this->handlePaymentPaid($data['data']);
                break;
            default:
                Log::info('Unhandled webhook event type', ['type' => $data['type']]);
        }

        return response()->json(['success' => true]);
    }


    private function handleCheckoutCompleted($data)
    {
        $checkoutId = $data['id'] ?? null;
        $referenceNumber = $data['attributes']['reference_number'] ?? null;
        $paymentId = $data['attributes']['payment_id'] ?? null;

        Log::info('Handling checkout completed', [
            'checkout_id' => $checkoutId,
            'reference_number' => $referenceNumber,
            'payment_id' => $paymentId
        ]);

        // Find the subscription by reference number or checkout ID
        $subscription = DistributorSubscription::where('reference_number', $referenceNumber)
            ->orWhere('checkout_id', $checkoutId)
            ->first();

        if (!$subscription) {
            Log::error('Subscription not found for checkout', [
                'checkout_id' => $checkoutId,
                'reference' => $referenceNumber
            ]);
            return;
        }

        // Calculate subscription duration based on plan
        $now = Carbon::now();
        $expiresAt = match ($subscription->plan) {
            '3_months' => $now->copy()->addMonths(3),
            '6_months' => $now->copy()->addMonths(6),
            '1_year' => $now->copy()->addYear(),
            default => $now->copy()->addMonth()
        };

        // Update all necessary fields
        $subscription->update([
            'payment_id' => $paymentId,
            'status' => 'active',
            'starts_at' => $now,
            'expires_at' => $expiresAt
        ]);

        Log::info('Subscription updated successfully', [
            'subscription_id' => $subscription->id,
            'payment_id' => $paymentId,
            'status' => $subscription->status
        ]);
    }

    private function handlePaymentPaid($data)
    {
        $paymentId = $data['id'] ?? null;

        Log::info('Handling payment.paid', ['payment_id' => $paymentId]);

        $subscription = DistributorSubscription::where('payment_id', $paymentId)->first();

        if (!$subscription) {
            Log::error('Subscription not found for payment ID', ['payment_id' => $paymentId]);
            return;
        }

        $now = Carbon::now();
        $expiresAt = match ($subscription->plan) {
            '3_months' => $now->copy()->addMonths(3),
            '6_months' => $now->copy()->addMonths(6),
            '1_year' => $now->copy()->addYear(),
            default => $now->copy()->addMonth()
        };

        $subscription->update([
            'status' => 'active',
            'starts_at' => $now,
            'expires_at' => $expiresAt
        ]);

        Log::info('Subscription activated successfully via payment.paid', [
            'subscription_id' => $subscription->id,
            'distributor_id' => $subscription->distributor_id,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s')
        ]);
    }
}
