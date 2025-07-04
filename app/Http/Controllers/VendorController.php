<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductUserResource;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function addProduct(Request $request)
    {
        $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'price' => 'required|numeric|min:0',
            'num_in_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();

        $brand = Brand::where('user_id', $user->id)->first();

        if (!$brand) {
            return response()->json(['message' => 'This user is not a brand owner'], 403);
        }

        $images = $request->file('images');

        $mainPhoto = $images[0]->store('products', 'public');

        $product = Product::create([
            'image' => $mainPhoto,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->subcategory_id,
            'price' => $request->price,
            'num_in_stock' => $request->num_in_stock,
            'description' => $request->description,
            'brand_id' => $brand->id,
        ]);

        foreach (array_slice($images, 1) as $image) {
            ProductPhoto::create([
                'product_id' => $product->id,
                'photo' => $image->store('product_photos', 'public'),
            ]);
        }

        return response()->json([
            'message' => 'Product created successfully',
            'product' => new ProductUserResource($product),
        ], 201);
    }

    //-------------------------------------------------------
    //---------------Get user with his related brand----------
    public function getAuthenticatedVendorWithBrand()
    {
        $user = Auth::user();

        // Find the brand where user_id matches the authenticated user
        $brand = Brand::where('user_id', $user->id)->first();

        if (!$brand) {
            return response()->json(['message' => 'No brand associated with this user'], 404);
        }

        return response()->json([
            'user' => $user,
            'brand' => $brand
        ]);
    }

    //--------------------------------------------------
    //-------------------Vendor Packages----------------
    public function getUserPackage()
    {
        $user = Auth::user();

        $subscription = $user->packageSubscription()->with('package')->first();

        if (!$subscription) {
            return response()->json(['message' => 'No package found for this user'], 404);
        }

        return response()->json([
            'package_name' => $subscription->package->name,
            'items' => $subscription->package->items,
            'price' => $subscription->package->price,
            'start_date' => $subscription->start_date,
            'end_date' => $subscription->end_date,
        ]);
    }

    //----------------------------------------------------

    public function getBrandOrders()
    {
        $user = Auth::user();
        $brand = $user->brand;

        if (!$brand) {
            return response()->json(['message' => 'No brand found for this user'], 404);
        }

        $productIds = $brand->products()->pluck('id');

        $orders = Order::whereHas('products', function ($query) use ($productIds) {
            $query->whereIn('products.id', $productIds);
        })
            ->with([
                'products' => function ($query) use ($productIds) {
                    $query->whereIn('products.id', $productIds);
                },
                'user' => function ($query) {
                    $query->select('id', 'name', 'address', 'phone', 'zip_code');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();


        return response()->json($orders);
    }

    //------------------------------------------------------------


    public function getProductDetails()
    {
        $products = Product::with(['category', 'subcategory', 'brand.user'])->where('acceptance_status','pending')->get();

        $productDetails = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'image' => $product->image,
                'description' => $product->description,
                'num_in_stock' => $product->num_in_stock,
                'category_name' => $product->category?->name,
                'sub_category_name' => $product->subCategory?->name,
                'price' => $product->price,
                'created_at' => $product->created_at,
                'acceptance_status' => $product->acceptance_status,
                'status' => $product->status,
                'brand_name' => $product->brand?->name,
                'vendor_name' => $product->brand?->user?->name,
            ];
        });

        return response()->json([
            'status' => 'success',
            'products' => $productDetails,
        ]);
    }

    //--------------------------------------------------------------------

    public function updateAcceptanceStatus(Request $request, $id)
    {
        $request->validate([
            'acceptance_status' => 'required|in:pending,accepted,rejected',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found.',
            ], 404);
        }

        $product->acceptance_status = $request->acceptance_status;
        $product->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Acceptance status updated successfully.',
            'product' => $product,
        ]);
    }

}
