<?php
namespace App\Http\Controllers;

use App\Http\Resources\{
    BrandResource,
    CategoryResource,
    AnnouncementResource,
    PostResource,
    CouponResource,
    ProductResource,
    InspirationResource
};
use App\Models\{Brand, Category, Announcement, Post, Coupon, Product, ProductPhoto, Review, Sale, Inspiration};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'brands' => BrandResource::collection(Brand::where('status', 1)->get()),
            'categories' => CategoryResource::collection(Category::where('status', 1)->get()),
            'announcements' => AnnouncementResource::collection(Announcement::where('status', 1)->get()),
            'posts' => PostResource::collection(Post::where('status', 1)->get()),
            'latest_post' => optional(Post::where('status', 1)->latest()->first(), fn ($post) => new PostResource($post)),
            'latest_coupon' => optional(Coupon::where('status', 1)->latest()->first(), fn ($coupon) => new CouponResource($coupon)),
            'super_deals' => ProductResource::collection(Product::where('status', 1)->where('is_in_super_deals', true)->get()),
            'mega_deals' => ProductResource::collection(Product::where('status', 1)->where('is_in_mega_deals', true)->get()),
            'on_sale' => ProductResource::collection(Product::where('status', 1)->whereIn('id', Sale::pluck('product_id'))->get()),
            'inspirations' => Inspiration::latest()->take(4)->get()->isNotEmpty()
                ? InspirationResource::collection(Inspiration::latest()->take(4)->get())
                : [],

            'new_arrivals' => Product::where('status', 1)->latest()->take(10)->get()->isNotEmpty()
                ? ProductResource::collection(Product::where('status', 1)->latest()->take(10)->get())
                : [],
        ]);
    }





    //------------------------------------------------------------------------
    //----------------------Show product details------------------------------
    public function show($id)
    {

        $product = Product::with('brand', 'category')->findOrFail($id);

        // 2. All additional product photos
        $photos = ProductPhoto::where('product_id', $product->id)->get()->map(function ($photo) {
            return asset('storage/' . $photo->photo);
        });

        // 3. All reviews for this product
        $reviews = Review::with('user:id,name')
        ->where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($review) {
                return [
                    'user' => $review->user->name ?? 'Unknown',
                    'title' => $review->title,
                    'rate' => $review->rate,
                ];
            });

        // 4. Rating percentage (1–5 stars)
        $ratingCounts = Review::select('rate', DB::raw('count(*) as count'))
            ->where('product_id', $product->id)
            ->groupBy('rate')
            ->pluck('count', 'rate');

        $totalReviews = $ratingCounts->sum();

        $ratingPercentages = [];

        foreach ([5, 4, 3, 2, 1] as $star) {
            $ratingPercentages[$star] = $totalReviews > 0
                ? round(($ratingCounts[$star] ?? 0) / $totalReviews * 100, 2)
                : 0;
        }

        return response()->json([
            'product' => new ProductResource($product),
            'photos' => $photos,
            'reviews' => $reviews,
            'rating_percentages' => $ratingPercentages,
        ]);
    }

    //-----------------------------------------------------------
    //-----------------------Similar Products--------------------
    public function similarProducts($productId)
    {
        $product = Product::findOrFail($productId);

        $similarProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('brand', 'category')
            ->take(8)
            ->latest()
            ->get();

        return ProductResource::collection($similarProducts);
    }

}

