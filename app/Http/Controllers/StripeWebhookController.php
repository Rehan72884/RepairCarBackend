<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $webhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // ✅ Listen to payment successful event
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // You can now mark the client_problem as paid, create a record, etc.
            Log::info('Payment successful: ' . $session->id);
        }

        return response()->json(['status' => 'success']);
    }
}