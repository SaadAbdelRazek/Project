<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchAll(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return response()->json(['message' => 'Search query is required'], 422);
        }

        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orWhereHas('brand', fn($q) => $q->where('name', 'like', "%{$query}%")
                ->where('status', 'enabled'))
            ->orWhereHas('category', fn($q) => $q->where('name', 'like', "%{$query}%")
                ->where('status', 1))
            ->orWhere('acceptance_status', 'accepted')
            ->orWhere('status', 'enabled')->get();

        $posts = Post::where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->where('status', 'enabled')
            ->take(5)->get();

        $categories = Category::where('name', 'like', "%{$query}%")
            ->where('status', 1)
            ->take(5)->get();

        $brands = Brand::where('name', 'like', "%{$query}%")
            ->where('status', 'enabled')
            ->take(5)->get();

        return response()->json([
            'products' => $products,
            'posts' => $posts,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }


    //----------------------------------------------------------------------
    public function filterProducts(Request $request)
    {
        $query = Product::query();

        if ($request->has('color')) {
            $query->where('color', $request->color);
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('category_ids')) {
            $query->whereIn('category_id', $request->category_ids);
        }

        if ($request->has('brand_ids')) {
            $query->whereIn('brand_id', $request->brand_ids);
        }

        if ($request->has('price_sort') && in_array($request->price_sort, ['asc', 'desc'])) {
            $query->orderBy('price', $request->price_sort);
        }

        $products = $query->get();

        return response()->json($products);
    }
}
