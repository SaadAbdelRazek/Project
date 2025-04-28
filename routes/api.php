<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/signup', [AdminController::class, 'signUp']);
Route::post('/login', [AdminController::class, 'signIn']);

Route::post('/forgot-password', [AdminController::class, 'sendResetLinkEmail']);

Route::post('/reset-password', [AdminController::class, 'resetPassword']);

Route::get('/users/order-stats', [UserController::class, 'getUsersWithOrderStats']);
Route::get('/users/{id}/order-stats', [UserController::class, 'getUserOrderStats']);
Route::get('/brands/{id}/stats', [BrandController::class, 'brandStats']);
Route::get('/brands/{id}/with-products', [BrandController::class, 'showWithProducts']);
Route::get('/admin/dashboard', [AdminController::class, 'dashboardStats']);



Route::apiResource('categories', CategoryController::class);

Route::apiResource('subcategories', SubcategoryController::class);

Route::apiResource('products', ProductController::class);

Route::apiResource('brands', BrandController::class);

Route::apiResource('posts', PostController::class);

Route::apiResource('announcements', AnnouncementController::class);

Route::apiResource('coupons', CouponController::class);

Route::apiResource('users', UserController::class);

Route::apiResource('reviews', ReviewController::class);

Route::apiResource('orders', OrderController::class);


