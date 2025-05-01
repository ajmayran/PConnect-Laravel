<?php

namespace App\Http\Controllers\Distributors;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DistributorSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $distributor = $user->distributor;

        // Check for active subscription
        $subscription = DistributorSubscription::where('distributor_id', $distributor->id)->first();

        return view('distributors.subscription', compact('subscription'));
    }

    public function createPaymongoCheckout(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:3_months,6_months,1_year',
            'amount' => 'required|numeric|min:1'
        ]);

        $user = Auth::user();
        $distributor = $user->distributor;

        if (!$distributor) {
            return response()->json([
                'success' => false,
                'message' => 'Distributor profile not found'
            ], 404);
        }

        // Generate a unique reference number
        $reference = 'SUB-' . strtoupper(Str::random(8)) . '-' . $distributor->id;

        // Create a pending subscription record
        $subscription = DistributorSubscription::create([
            'distributor_id' => $distributor->id,
            'plan' => $request->plan,
            'amount' => $request->amount,
            'reference_number' => $reference,
            'status' => 'pending'
        ]);

        // Calculate plan name for display
        $planName = match ($request->plan) {
            '3_months' => '3 Months',
            '6_months' => '6 Months',
            '1_year' => '1 Year',
            default => 'Subscription'
        };

        // Set up checkout data for Paymongo API call
        $checkoutData = [
            'data' => [
                'attributes' => [
                    'line_items' => [
                        [
                            'name' => "PConnect $planName Subscription",
                            'quantity' => 1,
                            'amount' => $request->amount * 100, // Paymongo expects amount in centavos
                            'currency' => 'PHP',
                            'description' => "PConnect Distributor $planName Subscription",
                        ],
                    ],
                    'payment_method_types' => ['gcash', 'card', 'paymaya'],
                    'success_url' => route('distributors.subscription.success', ['reference' => $reference]),
                    'cancel_url' => route('distributors.subscription.cancel', ['reference' => $reference]),
                    'reference_number' => $reference,
                    'description' => "PConnect Distributor $planName Subscription",
                    'send_email_receipt' => true,
                ]
            ]
        ];

        try {
            // Make API request to Paymongo
            $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                ->post('https://api.paymongo.com/v1/checkout_sessions', $checkoutData);

            $responseData = $response->json();

            // Log the response for debugging
            Log::info('Paymongo checkout response', [
                'response' => $responseData,
                'status' => $response->status()
            ]);

            if ($response->successful() && isset($responseData['data']['attributes']['checkout_url'])) {
                // Update subscription with checkout ID
                $subscription->update([
                    'checkout_id' => $responseData['data']['id']
                ]);

                return response()->json([
                    'success' => true,
                    'checkout_url' => $responseData['data']['attributes']['checkout_url']
                ]);
            } else {
                // Handle error in response
                return response()->json([
                    'success' => false,
                    'message' => 'Payment gateway error: ' . ($responseData['errors'][0]['detail'] ?? 'Unknown error')
                ], 500);
            }
        } catch (\Exception $e) {
            // Log exceptions
            Log::error('Paymongo checkout exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment service error: ' . $e->getMessage()
            ], 500);
        }
    }


    public function handleSuccess(Request $request, $reference)
    {
        $subscription = DistributorSubscription::where('reference_number', $reference)->first();

        if (!$subscription) {
            return redirect()->route('distributors.subscription')
                ->with('error', 'Subscription reference not found.');
        }

        try {
            // FIX 1: Corrected environment variable name
            $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY', ''), '')
                ->get("https://api.paymongo.com/v1/checkout_sessions/{$subscription->checkout_id}");

            $responseData = $response->json();

            Log::info('Payment verification response', [
                'response' => $responseData,
                'status' => $response->status()
            ]);

            if ($response->successful() && isset($responseData['data'])) {
                $sessionData = $responseData['data']['attributes'] ?? [];
                $paymentStatus = $sessionData['status'] ?? 'unknown';

                // FIX 2: Extract payment ID from the correct location
                $paymentId = null;

                // Try to get payment_intent data which contains payments array
                $paymentIntent = $sessionData['payment_intent'] ?? null;

                // Check if there are payments in the payment intent
                if (
                    $paymentIntent && isset($paymentIntent['attributes']['payments']) &&
                    !empty($paymentIntent['attributes']['payments'])
                ) {
                    $paymentId = $paymentIntent['attributes']['payments'][0]['id'] ?? null;
                }

                Log::info('Payment details extracted', [
                    'status' => $paymentStatus,
                    'payment_id' => $paymentId
                ]);

                // If payment is completed or we have a payment ID, activate the subscription
                if ($paymentStatus === 'paid' || $paymentId) {
                    $now = Carbon::now();
                    $expiresAt = match ($subscription->plan) {
                        '3_months' => $now->copy()->addMonths(3),
                        '6_months' => $now->copy()->addMonths(6),
                        '1_year' => $now->copy()->addYear(),
                        default => $now->copy()->addMonth()
                    };

                    $subscription->update([
                        'payment_id' => $paymentId,
                        'status' => 'active',
                        'starts_at' => $now,
                        'expires_at' => $expiresAt,
                    ]);

                    return redirect()->route('distributors.dashboard')
                        ->with('success', 'Thank you for subscribing! Your subscription is now active.');
                }
            }

            return redirect()->route('distributors.dashboard')
                ->with('info', 'Thank you for subscribing! Your payment is being processed. We will update your subscription status once the payment is confirmed.');
        } catch (\Exception $e) {
            Log::error('Error verifying payment status', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('distributors.subscription')
                ->with('error', 'An error occurred while verifying your payment. Please contact support.');
        }
    }


    public function handleCancel(Request $request, $reference)
    {
        // Find the subscription by reference number
        $subscription = DistributorSubscription::where('reference_number', $reference)->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'failed',
            ]);
        }

        return redirect()->route('distributors.subscription')
            ->with('warning', 'Your subscription payment was cancelled. Please try again.');
    }


    public function show()
    {
        $user = Auth::user();
        $distributor = $user->distributor;

        // Get all subscriptions for this distributor, ordered by creation date
        $subscriptions = DistributorSubscription::where('distributor_id', $distributor->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get active subscription
        $activeSubscription = $distributor->activeSubscription;

        // Get subscription history (excluding active subscription)
        $subscriptionHistory = $subscriptions->filter(function ($sub) use ($activeSubscription) {
            if (!$activeSubscription) return true;
            return $sub->id !== $activeSubscription->id;
        });

        return view('distributors.subscription.show', compact('activeSubscription', 'subscriptionHistory'));
    }
}
