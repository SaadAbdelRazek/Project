<?php

namespace App\Http\Controllers;
use App\Models\CartProduct;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function getCheckoutDetails()
    {
        $user = Auth::user();

        $cartItems = CartProduct::with('product')->where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->product->price * $item->quantity;
        }

        return response()->json([
            'products' => $cartItems,
            'total' => round($total, 2),
            'discount' => 0,
            'grand_total' => round($total, 2),
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $user = Auth::user();

        $cartItems = CartProduct::with('product')->where('user_id', $user->id)->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->product->price * $item->quantity;
        }

        $coupon = Coupon::where('code', $request->coupon_code)
            ->where('status', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();

        if (!$coupon) {
            return response()->json(['message' => 'Invalid coupon code.'], 400);
        }

        $usage = $coupon->users()->where('user_id', $user->id)->first();
        $usageCount = $usage ? $usage->pivot->usage_count : 0;

        if ($coupon->usage_limit && $usageCount >= $coupon->usage_limit) {
            return response()->json(['message' => 'Coupon usage limit exceeded.'], 400);
        }

        $discount = $coupon->type === 'percentage'
            ? $total * ($coupon->discount_value / 100)
            : $coupon->discount_value;

        $grandTotal = max($total - $discount, 0);

        $coupon->users()->syncWithoutDetaching([
            $user->id => ['usage_count' => $usageCount + 1]
        ]);

        return response()->json([
            'total' => round($total, 2),
            'discount' => round($discount, 2),
            'grand_total' => round($grandTotal, 2),
            'coupon' => $coupon->code,
        ]);
    }



}
