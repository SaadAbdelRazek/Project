<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
        return BrandResource::collection(Brand::latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|image',
            'user_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'priority'    => 'required|integer',
            'status'      => 'required|in:1,0',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('brands', 'public');
        }

        $brand = Brand::create($data);

        return new BrandResource($brand);
    }

    public function show($id)
    {
        return new BrandResource(Brand::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'user_id' => 'required|exists:users,id',
            'image'       => 'nullable|image',
            'description' => 'nullable|string',
            'priority'    => 'sometimes|integer',
            'status'      => 'sometimes|in:enabled,disabled',
        ]);

        if ($request->hasFile('image')) {
            if ($brand->image) {
                Storage::disk('public')->delete($brand->image);
            }
            $data['image'] = $request->file('image')->store('brands', 'public');
        }

        $brand->update($data);

        return new BrandResource($brand);
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        if ($brand->image) {
            Storage::disk('public')->delete($brand->image);
        }
        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully']);
    }

    //-------------------------------------------------------------------------

    public function brandStats($brandId)
    {
        $brand = Brand::with('products.orders')->findOrFail($brandId);

        $totalProducts = $brand->products->count();

        $totalRevenue = 0;

        foreach ($brand->products as $product) {
            $orderCount = $product->orders->count();
            $totalRevenue += $orderCount * $product->price;
        }

        return response()->json([
            'brand' => new BrandResource($brand),
            'total_products' => $totalProducts,
            'total_revenue' => $totalRevenue,
        ]);
    }

    //------------------------------------------------------

    public function showWithProducts($id)
    {
        $brand = Brand::findOrFail($id);
        $products = $brand->products;

        $productsCount   = $brand->products->count();
        $totalAmount   = $brand->products->sum('price');
        $avgOrderPrice = $productsCount > 0 ? round($totalAmount / $productsCount, 2) : 0;

        return response()->json([
            'brand' => new BrandResource($brand),
            'products' => ProductResource::collection($products),
                'num_of_products' => $productsCount,
        ]
        );
    }
}
