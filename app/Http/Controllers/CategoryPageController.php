<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryPageController extends Controller
{
    public function allCategories()
    {
        return CategoryResource::collection(
            Category::where('status', 1)->get()
        );
    }


    public function productsByCategory($id)
    {
        $category = Category::where('id', $id)->where('status', 1)->firstOrFail();

        $products = Product::where('category_id', $id)
            ->where('status', 1)
            ->get();

        return response()->json([
            'category' => new CategoryResource($category),
            'products' => ProductResource::collection($products),
        ]);
    }

}
