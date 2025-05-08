<?php

namespace App\Http\Controllers;

use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        return CouponResource::collection(Coupon::latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'           => 'required|string|unique:coupons,code|max:50',
            'type'           => 'required|in:percentage,fixed,free_shipping',
            'discount_value' => 'nullable|numeric|min:0',
            'usage_limit'    => 'nullable|integer|min:1',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'status'      => 'required|in:1,0',
        ]);

        $coupon = Coupon::create($data);
        return new CouponResource($coupon);
    }

    public function show($id)
    {
        $coupon = Coupon::findOrFail($id);
        return new CouponResource($coupon);
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $data = $request->validate([
            'code'           => 'sometimes|string|unique:coupons,code,' . $coupon->id,
            'type'           => 'sometimes|in:percentage,fixed,free_shipping',
            'discount_value' => 'nullable|numeric|min:0',
            'usage_limit'    => 'nullable|integer|min:1',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'status'         => 'sometimes|in:enabled,disabled',
        ]);

        $coupon->update($data);
        return new CouponResource($coupon);
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json(['message' => 'Coupon deleted successfully']);
    }
}
