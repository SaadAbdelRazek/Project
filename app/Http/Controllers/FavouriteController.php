<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use Illuminate\Http\Request;
use App\Http\Resources\FavouriteResource;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    public function index()
    {
        return FavouriteResource::collection(
            Favourite::with('product')->where('user_id', Auth::id())->get()
        );
    }

    public function store(Request $request)
    {
        $fav = Favourite::firstOrCreate([
            'user_id'    => Auth::id(),
            'product_id' => $request->product_id,
        ]);

        return new FavouriteResource($fav);
    }

    public function destroy($id)
    {
        $fav = Favourite::where('user_id', Auth::id())->findOrFail($id);
        $fav->delete();

        return response()->json(['message' => 'Removed from favourites']);
    }
}
