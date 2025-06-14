<?php

namespace App\Http\Controllers;

use App\Mail\TestEmail;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // Sign Up
    public function signUp(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'mobile' => 'required|string|unique:admins,mobile',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the admin user
        $admin = Admin::create([
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Signup successful',
            'user' => $admin,
        ], 201);
    }

    // Sign In
    public function signIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'remember_me' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate Sanctum token
        $tokenName = $request->remember_me ? 'admin-token-remembered' : 'admin-token';
        $token = $admin->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'admin' => $admin,
            'token' => $token,
        ]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // البحث عن المستخدم
        $user = Admin::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'This email is not registered'], 404);
        }

        // إنشاء التوكن
        $token = Str::random(60);

        // تخزين التوكن في قاعدة البيانات
        $user->reset_token = $token;
        $user->save();

        // إرسال الرابط مع التوكن
        $resetLink = url('http://localhost:3000/reset-password/' . $token);

        try {
            Mail::to($user->email)->send(new TestEmail($resetLink, $user->name));

            return response()->json(['status' => 'Reset link sent']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to send reset link. Please try again.',
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $reset = DB::table('admins')->where('reset_token', $request->token)->first();

        if (!$reset) {
            return response()->json(['error' => 'Invalid token'], 400);
        }

        $user = Admin::where('email', $reset->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        DB::table('admins')->where('email', $reset->email)->update(['reset_token' => null]);

        return response()->json(['message' => 'Password reset successful']);
    }


    public function logout(Request $request)
    {
        $admin = auth('sanctum')->user();

        if ($admin && $admin->currentAccessToken()) {
            $admin->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logged out successfully',
            ]);
        }

        return response()->json([
            'message' => 'Unauthenticated.',
        ], 401);
    }



    //------------------------------------------------------------------

    public function dashboardStats()
    {
        $totalOrders = Order::count();
        $totalBrands = Brand::count();
        $totalProducts = Product::count();
        $totalUsers = User::count();

        $pendingProducts = Product::where('acceptance_status', 'pending')->count();
        $acceptedProducts = Product::where('acceptance_status', 'accepted')->count();
        $rejectedProducts = Product::where('acceptance_status', 'rejected')->count();

        $pendingOrders = Order::where('order_status', 'pending')->count();
        $shippedOrders = Order::where('order_status', 'shipped')->count();

        //-----------------------------------------------------------------------

        $totalOrdersValue = Order::sum('total_price');

        $siteSharePercentage = 0.10;
        $siteShare = $totalOrdersValue * $siteSharePercentage;

        $commissionPercentage = 0.40;
        $deliveryPercentage = 0.25;
        $taxPercentage = 0.20;
        $pendingPercentage = 0.15;

        $commission = round($siteShare * $commissionPercentage, 2);
        $delivery = round($siteShare * $deliveryPercentage, 2);
        $tax = round($siteShare * $taxPercentage, 2);
        $pending = round($siteShare * $pendingPercentage, 2);

        $inHouseEarnings = round($commission + $delivery + $tax + $pending, 2);

        //-----------------------------------------------------------------------

        $onlineThreshold = Carbon::now()->subMinutes(5);

        $onlineUsersCount = User::where('last_seen', '>=', $onlineThreshold)->count();

        //-----------------------------------------------------------------------

        $orders = Order::whereYear('created_at', now()->year)
            ->where('is_paid', 1)
            ->get();

        $monthlyStats = [];

        foreach (range(1, 12) as $month) {
            $monthlyStats[$month] = [
                'inhouse' => 0,
                'vendors' => 0,
            ];
        }

        foreach ($orders as $order) {
            $month = $order->created_at->format('n'); // 1-12

            $total = $order->total_price;
            $adminShare = $total * 0.10;
            $vendorShare = $total * 0.90;

            $monthlyStats[$month]['inhouse'] += $adminShare;


            $monthlyStats[$month]['vendors'] += $vendorShare;
        }

        $formattedStats = [];

        foreach (range(1, 12) as $month) {
            $formattedStats[] = [
                'month' => date('M', mktime(0, 0, 0, $month, 1)),
                'inhouse' => round($monthlyStats[$month]['inhouse'], 2),
                'vendors' => round($monthlyStats[$month]['vendors']),
            ];
        }

        //-----------------------------------------------------------------------

        $customerCount = User::where('role', 'user')->count();
        $vendorCount = User::where('role', 'vendor')->count();

        //-----------------------------------------------------------------------

        $usersWithOrderCount = User::withCount('orders')
            ->orderByDesc('orders_count')
            ->get();

        //-----------------------------------------------------------------------

        $topBrandsSellingProducts = DB::table('order_product')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->select('brands.name','brands.image', DB::raw('SUM(order_product.quantity) as total_sold'))
            ->groupBy('brands.id', 'brands.name','brands.image')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        //------------------------------------------------------------------------

        $topBrandsIncome = DB::table('order_product')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->select('brands.name','brands.image', DB::raw('SUM(order_product.quantity * order_product.price) as total_revenue'))
            ->groupBy('brands.id', 'brands.name','brands.image')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        //-------------------------------------------------------------------------

        $topFiveReviewsProducts = DB::table('reviews')
            ->join('products', 'reviews.product_id', '=', 'products.id')
            ->select('products.id', 'products.name', 'products.image', DB::raw('AVG(reviews.rate) as average_rating'), DB::raw('COUNT(reviews.id) as total_reviews'))
            ->groupBy('products.id', 'products.name', 'products.image')
            ->orderByDesc('average_rating')
            ->orderByDesc('total_reviews')
            ->limit(5)
            ->get();

        //-------------------------------------------------------------------------

        $topFiveSellingProducts = DB::table('order_product')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->select('products.id', 'products.name', 'products.image', DB::raw('SUM(order_product.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name', 'products.image')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        //-------------------------------------------------------------------------

        return response()->json([
            'total_orders' => $totalOrders,
            'total_brands' => $totalBrands,
            'total_products' => $totalProducts,
            'total_users' => $totalUsers,
            'pending_products' => $pendingProducts,
            'accepted_products' => $acceptedProducts,
            'rejected_products' => $rejectedProducts,
            'pending_orders' => $pendingOrders,
            'shipped_orders' => $shippedOrders,
            'total_orders_value' => round($totalOrdersValue, 2),
            'site_share' => round($siteShare, 2),
            'commission_earned' => $commission,
            'delivery_charge_earned' => $delivery,
            'total_tax_collected' => $tax,
            'pending_amount' => $pending,
            'in_house_earnings' => $inHouseEarnings,
            'online_users' => $onlineUsersCount,
            'income_statistics' => $formattedStats,
            'number_of_customers' => $customerCount,
            'number_of_vendors' => $vendorCount,
            'top_five_customers' => $usersWithOrderCount,
            'top_five_brands_selling_number_of_products' => $topBrandsSellingProducts,
            'top_five_brands_getting_money' => $topBrandsIncome,
            'top_five_products_in_reviews' => $topFiveReviewsProducts,
            'top_five_selling_products' => $topFiveSellingProducts,
        ]);
    }

    public function getAllUsersWithUserRole()
    {
        $users = User::where('role', 'user')->get();

        return response()->json([
            'status' => 'success',
            'customers' => $users,
        ]);
    }

    public function getAllUsersWithVendorRole()
    {
        $vendors = User::where('role', 'vendor')
            ->with(['brand.products.orders'])
            ->get()
            ->map(function ($vendor) {
                $brand = $vendor->brand;

                $productCount = $brand?->products?->count() ?? 0;

                $totalPaid = 0;

                if ($brand && $brand->products) {
                    foreach ($brand->products as $product) {
                        foreach ($product->orders as $order) {
                            $totalPaid += $order->pivot->price * $order->pivot->quantity;
                        }
                    }
                }

                return [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'email' => $vendor->email,
                    'phone' => $vendor->phone,
                    'brand_name' => $brand?->name ?? null,
                    'product_count' => $productCount,
                    'total_paid' => $totalPaid,
                    'registered_at' => $vendor->created_at,
                ];
            });

        return response()->json([
            'status' => 'success',
            'vendors' => $vendors,
        ]);
    }


}


