<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategoryPageController;
use App\Http\Controllers\ChatHistoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\GeminiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InspirationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PersonalityController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPhotoController;
use App\Http\Controllers\QuizPersonalityController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use GeminiAPI\Laravel\Facades\Gemini;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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


Route::middleware(['auth:sanctum', 'isAdmin'])->post('/logout', [AdminController::class, 'logout']);


Route::post('/signup', [AdminController::class, 'signUp']);
Route::post('/login', [AdminController::class, 'signIn']);

Route::post('/forgot-password', [AdminController::class, 'sendResetLinkEmail']);

Route::post('/reset-password', [AdminController::class, 'resetPassword']);

Route::middleware(['isAdmin'])->group(function () {

    Route::get('/vendor-panel', [VendorController::class, 'getProductDetails']);
    Route::put('/products/{id}/acceptance-status', [VendorController::class, 'updateAcceptanceStatus']);

    Route::get('/users/order-stats', [UserController::class, 'getUsersWithOrderStats']);
    Route::get('/users/{id}/order-stats', [UserController::class, 'getUserOrderStats']);
    Route::get('/brands/{id}/stats', [BrandController::class, 'brandStats']);
    Route::get('/brands/{id}/with-products', [BrandController::class, 'showWithProducts']);
    Route::get('/admin/dashboard', [AdminController::class, 'dashboardStats']);

    Route::apiResource('categories', CategoryController::class);

    Route::apiResource('subcategories', SubcategoryController::class);

    Route::apiResource('products', ProductController::class);

    Route::apiResource('brands', BrandController::class)->only(['index', 'destroy','update']);

    Route::apiResource('posts', PostController::class);

    Route::apiResource('announcements', AnnouncementController::class);

    Route::apiResource('coupons', CouponController::class);

    Route::apiResource('sales', SaleController::class);

    Route::apiResource('inspirations', InspirationController::class)->only(['store', 'destroy']);

    Route::apiResource('packages', PackageController::class)->only(['store', 'destroy', 'update']);

    Route::apiResource('users', UserController::class)->only(['index', 'destroy','update']);

    Route::apiResource('reviews', ReviewController::class);


    Route::post('/questions', [PersonalityController::class, 'storeQuestion']);

    Route::put('/questions/{questionId}', [PersonalityController::class, 'updateQuestion']);

    Route::delete('/questions/{questionId}', [PersonalityController::class, 'deleteQuestion']);


    Route::get('personalities', [QuizPersonalityController::class, 'index']);
    Route::post('personalities', [QuizPersonalityController::class, 'store']);
    Route::put('personalities/{id}', [QuizPersonalityController::class, 'update']);
    Route::delete('personalities/{id}', [QuizPersonalityController::class, 'destroy']);

    Route::get('/admin/customers', [AdminController::class, 'getAllUsersWithUserRole']);
    Route::get('/admin/vendors', [AdminController::class, 'getAllUsersWithVendorRole']);

});
Route::apiResource('orders', OrderController::class);

Route::apiResource('brands', BrandController::class);
Route::get('personalities/{id}', [QuizPersonalityController::class, 'show']);

Route::get('/questions', [PersonalityController::class, 'getAllQuestions']);

Route::apiResource('inspirations', InspirationController::class)->only(['index']);

Route::apiResource('packages', PackageController::class)->only(['index']);


//------------------------User----------------------------------------

Route::post('/user/signup', [AuthController::class, 'signUp']);

Route::post('/user/login', [AuthController::class, 'login'])->name('login');

Route::post('/user/forgot-password', [AuthController::class, 'sendResetLinkEmail']);

Route::post('/user/reset-password', [AuthController::class, 'reset']);


// Verification Route
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json(['message' => 'Email verified successfully']);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

Route::middleware(['auth:sanctum'])->group(function () {

    Route::apiResource('users', UserController::class)->only(['show']);


    Route::post('/logout', [AuthController::class, 'logout']);

    Route::patch('/user/update', [AuthController::class, 'update']);

    Route::post('/email/resend', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification email resent.']);
    });

    Route::get('/cart', [CartProductController::class, 'index']);
    Route::post('/cart', [CartProductController::class, 'store']);
    Route::put('/cart/{id}', [CartProductController::class, 'update']);
    Route::delete('/cart/{id}', [CartProductController::class, 'destroy']);

    Route::get('/favourites', [FavouriteController::class, 'index']);
    Route::post('/favourites', [FavouriteController::class, 'store']);
    Route::delete('/favourites/{id}', [FavouriteController::class, 'destroy']);

    //--------------------------DONE----------------------------------------------------------------------

    Route::get('/compare', [CompareController::class, 'index']);
    Route::post('/compare', [CompareController::class, 'store']);
    Route::delete('/compare/{id}', [CompareController::class, 'destroy']);
    Route::delete('/compare', [CompareController::class, 'clear']);

    Route::post('/vendor/add-product', [VendorController::class, 'addProduct']);

});
//--------------------------DONE----------------------------------------------------------------------



Route::get('/home', [HomeController::class, 'index']);

Route::get('/esaltare/categories', [CategoryPageController::class, 'allCategories']);
Route::get('/esaltare/categories/{id}/products', [CategoryPageController::class, 'productsByCategory']);
//--------------------------DONE----------------------------------------------------------------------



Route::middleware('auth:sanctum')->get('/checkout/details', [CheckoutController::class, 'getCheckoutDetails']);
Route::middleware('auth:sanctum')->post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/products/photos', [ProductPhotoController::class, 'store']);
    Route::delete('/products/photos/{id}', [ProductPhotoController::class, 'destroy']);
});
Route::get('/products/{id}/photos', [ProductPhotoController::class, 'index']);


Route::get('/view/products/{id}', [HomeController::class, 'show'])->name('productDetails');
Route::get('products/{id}/similar', [HomeController::class, 'similarProducts']);

//--------------------------DONE----------------------------------------------------------------------



Route::middleware('auth:sanctum')->get('/vendor/profile', [VendorController::class, 'getAuthenticatedVendorWithBrand']);

Route::middleware('auth:sanctum')->get('/vendor/package', [VendorController::class, 'getUserPackage']);

Route::middleware('auth:sanctum')->get('/vendor/orders', [VendorController::class, 'getBrandOrders']);

Route::middleware('auth:sanctum')->get('/user/orders', [UserController::class, 'getUserOrders']);

Route::middleware('auth:sanctum')->get('/user/orders/{order_id}', [UserController::class, 'getUserOrderByNumber']);


//---------------search-------------------------------
Route::post('/search/all', [SearchController::class, 'searchAll']);

Route::get('/esaltare/products/filter', [SearchController::class, 'filterProducts']);
//--------------------------------------------------------------------------------

Route::middleware('auth:sanctum')->post('/gemini-response', [GeminiController::class, 'handlePrompt']);


//-------------
Route::middleware('auth:sanctum')->get('/chat-history', [ChatHistoryController::class, 'index']);
//-------------
Route::middleware('auth:sanctum')->get('/auth/user', [UserController::class, 'getAuthenticatedUserData']);
//-------------
Route::middleware('auth:sanctum')->post('/strip/checkout', [PaymentController::class, 'createCheckoutSession']);
//-------------
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
//-------------
Route::middleware('auth:sanctum')->post('/contact', [ContactController::class, 'store']);
//------------------
Route::middleware('auth:sanctum')->get('/admin/contacts', [ContactController::class, 'index']);
//------------------
Route::middleware('auth:sanctum')->post('/user/add-review', [ReviewController::class, 'addReview']);
//------------------
Route::middleware('auth:sanctum')->post('/package/subscribe', [PackageController::class, 'createPackageCheckoutSession']);
