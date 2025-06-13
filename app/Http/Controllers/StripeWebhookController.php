<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        if (!$sigHeader) {
            Log::error('Stripe Webhook: Missing Signature Header');
            return response()->json(['error' => 'Signature header missing'], 400);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (UnexpectedValueException $e) {
            Log::error('Stripe Webhook: Invalid Payload - ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe Webhook: Invalid Signature - ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            Log::info('Stripe Webhook: Checkout session completed', [
                'session_id' => $session->id ?? null,
                'order_id' => $session->metadata->order_id ?? 'missing',
            ]);

            $orderId = $session->metadata->order_id ?? null;

            if ($orderId) {
                $updated = Order::where('id', $orderId)->update(['is_paid' => true]);
                Log::info("Stripe Webhook: Order #{$orderId} update status: " . ($updated ? 'SUCCESS' : 'FAILED'));
            } else {
                Log::warning('Stripe Webhook: Missing order_id in metadata.');
            }
        } else {
            Log::info("Stripe Webhook: Event {$event->type} received but not handled.");
        }

        return response()->json(['status' => 'success']);
    }
}
