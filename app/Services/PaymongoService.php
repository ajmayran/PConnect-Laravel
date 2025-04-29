<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymongoService
{
    protected $secretKey;
    protected $baseUrl = 'https://api.paymongo.com/v1';
    
    public function __construct()
    {
        $this->secretKey = config('services.paymongo.secret_key', env('PAYMONGGO_SECRET_KEY'));
    }

    /**
     * Create a checkout session
     */
    public function createCheckout($data)
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post($this->baseUrl . '/checkout_sessions', [
                    'data' => [
                        'attributes' => [
                            'line_items' => [
                                [
                                    'name' => $data['name'],
                                    'quantity' => 1,
                                    'amount' => $data['amount'] * 100, // Paymongo expects amount in centavos
                                    'currency' => 'PHP',
                                    'description' => $data['description'],
                                ],
                            ],
                            'payment_method_types' => ['gcash', 'card', 'paymaya'],
                            'success_url' => $data['success_url'],
                            'cancel_url' => $data['cancel_url'],
                            'reference_number' => $data['reference_number'],
                            'description' => $data['description'],
                            'send_email_receipt' => true,
                        ],
                    ],
                ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            Log::error('Paymongo checkout error', [
                'response' => $response->json(),
                'status' => $response->status()
            ]);
            
            return null;
        } catch (\Exception $e) {
            Log::error('Paymongo service exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }

    /**
     * Verify a webhook signature
     */
    public function verifyWebhookSignature($payload, $sigHeader)
    {
        $webhookSecret = config('services.paymongo.webhook_secret', env('PAYMONGO_WEBHOOK_SECRET'));
        
        if (!$webhookSecret) {
            Log::error('Missing webhook secret');
            return false;
        }

        $computedSignature = hash_hmac('sha256', $payload, $webhookSecret);
        
        return hash_equals($computedSignature, $sigHeader);
    }
}