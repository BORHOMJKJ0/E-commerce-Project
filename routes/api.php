<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Contact\ContactInformationController;
use App\Http\Controllers\Contact\ContactTypeController;
use App\Http\Controllers\Offer\OfferController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Warehouse\WarehouseController;
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
Route::middleware('api')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('warehouses', WarehouseController::class);
    Route::apiResource('offers', OfferController::class);
    Route::prefix('products')->controller(ProductController::class)->group(function () {
        Route::get('/order/{column}/{direction}', 'orderBy');
    });
    Route::prefix('categories')->controller(CategoryController::class)->group(function () {
        Route::get('/order/{column}/{direction}', 'orderBy');
    });
    Route::prefix('warehouses')->controller(WarehouseController::class)->group(function () {
        Route::get('/order/{column}/{direction}', 'orderBy');
    });
    Route::prefix('offers')->controller(OfferController::class)->group(function () {
        Route::get('/order/{column}/{direction}', 'orderBy');
    });
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('api')->prefix('users')->group(function () {

    Route::controller(UserController::class)->group(function () {
        Route::get('all', 'index');
        Route::post('register', 'register');
        Route::post('login', 'login')->middleware('verified.email');
        Route::post('logout', 'logout');
        Route::get('profile/{user_id}', 'profile')->whereNumber('user_id');
        Route::patch('update', 'update');
    });

    Route::controller(EmailVerificationController::class)->group(function () {
        Route::get('email-verification/{email}', 'sendEmailVerification');
        Route::post('email-verification', 'email_verification');
    });

    Route::post('password/forget-password', [ForgetPasswordController::class, 'forgetPassword']);
    Route::post('password/reset', [ResetPasswordController::class, 'resetPassword']);

    Route::controller(ContactInformationController::class)->prefix('contact')->group(function () {
        Route::post('add', 'store');
        Route::get('show/{user_id}', 'show')->whereNumber('user_id');
        Route::delete('remove/{contact_information_id}', 'destroy')->whereNumber('contact_information_id');
        Route::delete('remove-all', 'destroyAll');
    });

    Route::controller(ContactTypeController::class)->prefix('contact-type')->group(function () {
        Route::get('contact-type-all', 'index');
        Route::get('contact-type/{id}', 'show')->whereNumber('id');
    });
});
