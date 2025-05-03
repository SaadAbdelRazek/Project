<?php


namespace App\Http\Controllers;

use App\Http\Resources\ProductPhotoResource;
use App\Models\ProductPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductPhotoController extends Controller
{
    public function index($productId)
    {
        $photos = ProductPhoto::where('product_id', $productId)->get();
        return ProductPhotoResource::collection($photos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'photos.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $savedPhotos = [];

        foreach ($request->file('photos') as $photo) {
            $path = $photo->store('products', 'public'); // save in storage/app/public/products

            $savedPhotos[] = ProductPhoto::create([
                'product_id' => $request->product_id,
                'photo' => $path,
            ]);
        }

        return ProductPhotoResource::collection($savedPhotos);
    }

    public function destroy($id)
    {
        $photo = ProductPhoto::findOrFail($id);

        // Delete from storage
        if (Storage::disk('public')->exists($photo->photo)) {
            Storage::disk('public')->delete($photo->photo);
        }

        $photo->delete();

        return response()->json(['message' => 'Photo deleted successfully']);
    }
}
