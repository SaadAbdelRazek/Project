<?php

namespace App\Http\Controllers;
use App\Models\CartProduct;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $user = Auth::user();

        // Step 1: Get cart products
        $cartItems = CartProduct::with('product')->where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        // Step 2: Calculate total
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->product->price * $item->quantity;
        }

        // Step 3: Apply coupon logic (refactored)
        [$discount, $grandTotal] = $this->applyCoupon($request->coupon_code ?? null, $total);

        return response()->json([
            'products' => $cartItems,
            'total' => round($total, 2),
            'discount' => round($discount, 2),
            'grand_total' => round($grandTotal, 2),
        ]);
    }

    /**
     * Apply a coupon to the total and return [discount, grandTotal].
     */
    private function applyCoupon(?string $code, float $total): array
    {
        if (!$code) {
            return [0, $total];
        }

        $user = Auth::user();

        // Retrieve the coupon
        $coupon = Coupon::where('code', $code)
            ->where('status', 'enabled')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();

        if (!$coupon) {
            return [0, $total]; // Or throw an error, depends on your logic
        }

        // Get the usage count from the pivot table
        $usage = $coupon->users()->where('user_id', $user->id)->first();
        $usageCount = $usage ? $usage->pivot->usage_count : 0;

        // Check usage limit
        if ($coupon->usage_limit && $usageCount >= $coupon->usage_limit) {
            return [0, $total]; // Or throw an error if needed
        }

        // Calculate discount
        $discount = 0;
        if ($coupon->type === 'percentage') {
            $discount = $total * ($coupon->discount_value / 100);
        } elseif ($coupon->type === 'fixed') {
            $discount = $coupon->discount_value;
        }

        $grandTotal = max($total - $discount, 0);

        // Save or update the usage count in the pivot table
        $coupon->users()->syncWithoutDetaching([
            $user->id => ['usage_count' => $usageCount + 1]
        ]);

        return [$discount, $grandTotal];
    }


}
