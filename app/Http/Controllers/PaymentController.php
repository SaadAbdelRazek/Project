<?php

namespace App\Http\Controllers;
use App\Models\CartProduct;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        $user = Auth::user();

        Stripe::setApiKey(config('services.stripe.secret'));

        $cartItems = CartProduct::with('product')->where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $total = 0;
        foreach ($cartItems as $item) {
            $subtotal = $item->product->price * $item->quantity;
            $total += $subtotal;
        }

        $discount = 0;
        $couponCode = $request->coupon_code;
        $address = $request->address;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)
                ->where('status', 1)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->first();

            if ($coupon) {
                $usage = $coupon->users()->where('user_id', $user->id)->first();
                $usageCount = $usage ? $usage->pivot->usage_count : 0;

                if (!$coupon->usage_limit || $usageCount < $coupon->usage_limit) {
                    $discount = $coupon->type === 'percentage'
                        ? $total * ($coupon->discount_value / 100)
                        : $coupon->discount_value;

                    $coupon->users()->syncWithoutDetaching([
                        $user->id => ['usage_count' => $usageCount + 1]
                    ]);
                }
            }
        }

        $grandTotal = max($total - $discount, 0);
        $discountRatio = $discount / $total;

        // بناء line_items بعد خصم نسبة الخصم من كل منتج
        $lineItems = [];

        foreach ($cartItems as $item) {
            $originalPrice = $item->product->price;
            $discountedPrice = $originalPrice * (1 - $discountRatio);
            $unitAmount = intval(round($discountedPrice * 100)); // Stripe requires cents

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => $unitAmount,
                    'product_data' => [
                        'name' => $item->product->name,
                    ],
                ],
                'quantity' => $item->quantity,
            ];
        }

        $lastOrder = Order::latest('id')->first();
        $nextOrderNum = $lastOrder ? $lastOrder->order_num + 68 : 1500;

        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $grandTotal,
            'address' => $address,
            'order_num' => $nextOrderNum,
            'status' => $request->status ?? 'placed',
            'order_status' => $request->order_status ?? 'pending',
            'is_paid' => 0,
        ]);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => 'http://localhost:5173/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'http://localhost:5173/cancel',
            'metadata' => [
                'order_id' => $order->id,
                'user_id' => $user->id,
            ],
        ]);

        return response()->json(['url' => $session->url]);
    }

}
