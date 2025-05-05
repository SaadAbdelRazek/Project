<?php
namespace App\Http\Controllers;

use App\Http\Resources\CompareResource;
use App\Models\CompareProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompareController extends Controller
{
    public function index()
    {
        $items = CompareProduct::with('product')->where('user_id', Auth::id())->get();
        return CompareResource::collection($items);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = Auth::user();
        $existing = CompareProduct::where('user_id', $user->id)->count();

        if ($existing >= 4) {
            return response()->json(['message' => 'You can only compare up to 4 products'], 422);
        }

        $exists = CompareProduct::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Product already in comparison'], 422);
        }

        $compare = CompareProduct::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
        ]);

        return new CompareResource($compare);
    }

    public function destroy($id)
    {
        $compare = CompareProduct::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$compare) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $compare->delete();

        return response()->json(['message' => 'Removed from comparison']);
    }

    public function clear()
    {
        CompareProduct::where('user_id', Auth::id())->delete();
        return response()->json(['message' => 'Comparison list cleared']);
    }
}
