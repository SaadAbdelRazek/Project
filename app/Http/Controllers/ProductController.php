<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
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
            'image'           => 'nullable|image',
            'description'     => 'nullable|string',
            'price'           => 'required|numeric',
            'width'           => 'nullable|numeric',
            'height'          => 'nullable|numeric',
            'length'          => 'nullable|numeric',
            'num_in_stock'    => 'required|integer',
            'status'      => 'required|in:1,0',
            'priority'        => 'required|integer',
            'category_id'     => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:subcategories,id',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        return new ProductResource($product->load(['category', 'subcategory']));
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
            'status'          => 'sometimes|in:enabled,disabled',
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
