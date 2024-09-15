<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\contact\ContactInformationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarehouseController;
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

Route::apiResource('products', ProductController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('warehouses', WarehouseController::class);
Route::prefix('products')->controller(ProductController::class)->group(function () {
    Route::get('/order/{column}/{direction}', 'orderBy');
});
Route::prefix('categories')->controller(CategoryController::class)->group(function () {
    Route::get('/order/{column}/{direction}', 'orderBy');
});
Route::prefix('warehouses')->controller(WarehouseController::class)->group(function () {
    Route::get('/order/{column}/{direction}', 'orderBy');
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('password/forget-password', [ForgetPasswordController::class, 'forgetPassword']);
Route::post('password/reset', [ResetPasswordController::class, 'resetPassword']);
Route::prefix('users')->group(function () {
    Route::get('email-verification', [EmailVerificationController::class, 'sendEmailVerification']);
    Route::post('email-verification', [EmailVerificationController::class, 'email_verification']);
});

Route::group(['middleware' => 'api', 'prefix' => 'users'], function () {
    Route::get('show-all-users', [UserController::class, 'show_all_users']);
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login'])->middleware('verified.email');
    Route::post('logout', [UserController::class, 'logout']);
    Route::get('profile', [UserController::class, 'profile']);
    Route::put('updateUser', [UserController::class, 'updateUser']);
    Route::post('contact-information', [ContactInformationController::class, 'addContact']);
    Route::get('contact-information', [ContactInformationController::class, 'show']);
    Route::delete('contact-information', [ContactInformationController::class, 'delete_certain_contact']);
    Route::delete('contact-information_all', [ContactInformationController::class, 'delete_all_contact']);
});
