<?php

namespace App\Http\Controllers;

use App\Models\CartProduct;
use Illuminate\Http\Request;
use App\Http\Resources\CartProductResource;
use Illuminate\Support\Facades\Auth;

class CartProductController extends Controller
{
    public function index()
    {
        return CartProductResource::collection(
            CartProduct::with('product')->where('user_id', Auth::id())->get()
        );
    }

    public function store(Request $request)
    {
        $cart = CartProduct::updateOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $request->product_id],
            ['quantity' => $request->quantity ?? 1],
        );

        return new CartProductResource($cart);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = CartProduct::where('user_id', Auth::id())->findOrFail($id);
        $cart->update(['quantity' => $request->quantity]);

        return response()->json(['message' => 'Quantity updated', 'cart' => new CartProductResource($cart)]);
    }

    public function destroy($id)
    {
        $cart = CartProduct::where('user_id', Auth::id())->findOrFail($id);
        $cart->delete();

        return response()->json(['message' => 'Removed from cart']);
    }
}
