<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            Log::info('Webhook Received', [
                'event_type' => $event->type,
                'order_id' => $session->metadata->order_id ?? 'null',
            ]);

            $orderId = $session->metadata->order_id ?? null;

            if ($orderId) {
                $updated=Order::where('id', $orderId)->update(['is_paid' => 1]);
                Log::info("Order update result: $updated");
            }
        }


        return response()->json(['status' => 'success']);
    }
}
