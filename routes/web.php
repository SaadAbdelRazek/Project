<?php

use App\Http\Controllers\FeatureController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/generate-image', [FeatureController::class, 'showForm'])->name('image.form');
Route::post('/generate-image', [FeatureController::class, 'generateImage'])->name('generate.image');


Route::get('/test-key', function () {
    dd(config('services.gemini.api_key'));
});


Route::get('/payment-success', function () {
    return view('success');
})->name('payment.success');

Route::get('/payment-cancel', function () {
    return view('cancel');
})->name('payment.cancel');
