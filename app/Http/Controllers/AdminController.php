<?php

namespace App\Http\Controllers;

use App\Mail\TestEmail;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
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

        // عدد المنتجات بناءً على حالتها
        $pendingProducts = Product::where('acceptance_status', 'pending')->count();
        $acceptedProducts = Product::where('acceptance_status', 'accepted')->count();
        $rejectedProducts = Product::where('acceptance_status', 'rejected')->count();

        // عدد الطلبات بناءً على حالتها
        $pendingOrders = Order::where('order_status', 'pending')->count();
        $shippedOrders = Order::where('order_status', 'shipped')->count();

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
        ]);
    }

}


