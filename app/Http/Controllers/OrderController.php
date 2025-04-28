<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return OrderResource::collection(Order::with(['user', 'products'])->latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'total_price' => 'required|numeric',
            'order_status' => 'in:pending,shipped',
            'is_paid' => 'required|boolean',
            'address' => 'nullable|string',
            'status' => 'in:Active,Inactive',
        ]);

        $lastOrder = Order::latest('id')->first();
        $nextOrderNum = $lastOrder ? $lastOrder->order_num + 68 : 1500;

        $order = Order::create([
            'user_id' => $request->user_id,
            'order_num' => $nextOrderNum,
            'total_price' => $request->total_price,
            'order_status' => $request->order_status ?? 'pending',
            'is_paid' => $request->is_paid,
            'address' => $request->address,
            'status' => $request->status ?? 'Active',
        ]);

        $order->products()->attach($request->product_ids);

        return new OrderResource($order->load(['user', 'products']));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'products'])->findOrFail($id);
        return new OrderResource($order);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        // Detach all related products
        $order->products()->detach();

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully.']);
    }

}
