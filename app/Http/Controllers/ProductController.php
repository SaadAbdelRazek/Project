<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return ProductResource::collection(
            Product::with(['category', 'subcategory'])->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'images'          => 'required|array|min:1',
            'images.*'        => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'description'     => 'nullable|string',
            'price'           => 'required|numeric',
            'width'           => 'nullable|numeric',
            'height'          => 'nullable|numeric',
            'length'          => 'nullable|numeric',
            'num_in_stock'    => 'required|integer',
            'status'          => 'required|in:1,0',
            'priority'        => 'required|integer',
            'category_id'     => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:subcategories,id',
        ]);

        $images = $request->file('images');

        $data['image'] = $images[0]->store('products', 'public');

        $product = Product::create($data);

        foreach (array_slice($images, 1) as $image) {
            ProductPhoto::create([
                'product_id' => $product->id,
                'photo' => $image->store('product_photos', 'public'),
            ]);
        }

        return new ProductResource($product->load(['category', 'subcategory', 'photos']));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'subcategory'])->findOrFail($id);
        return new ProductResource($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'name'            => 'sometimes|string|max:255',
            'image'           => 'nullable|image',
            'description'     => 'nullable|string',
            'price'           => 'sometimes|numeric',
            'width'           => 'nullable|numeric',
            'height'          => 'nullable|numeric',
            'length'          => 'nullable|numeric',
            'num_in_stock'    => 'sometimes|integer',
            'status'          => 'sometimes|in:1,0',
            'priority'        => 'sometimes|integer',
            'category_id'     => 'sometimes|exists:categories,id',
            'sub_category_id' => 'sometimes|exists:subcategories,id',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return new ProductResource($product->load(['category', 'subcategory']));
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    }
}
