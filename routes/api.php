<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\contact\ContactInformationController;
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

Route::post('password/forget-password', [ForgetPasswordController::class, 'forgetPassword']);
Route::post('password/reset', [ResetPasswordController::class, 'resetPassword']);

Route::group(['middleware' => 'api', 'prefix' => 'users'], function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login'])->middleware('verified');
    Route::post('logout', [UserController::class, 'logout']);
    Route::get('profile', [UserController::class, 'profile'])->middleware('verified.email');
    Route::put('updateUser', [UserController::class, 'updateUser']);
    Route::get('email-verification', [EmailVerificationController::class, 'sendEmailVerification']);
    Route::post('email-verification', [EmailVerificationController::class, 'email_verification']);
    Route::post('contact-information', [ContactInformationController::class, 'addContact']);
    Route::get('contact-information', [ContactInformationController::class, 'show']);
    Route::delete('contact-information', [ContactInformationController::class, 'delete_certain_contact']);
    Route::delete('contact-information_all', [ContactInformationController::class, 'delete_all_contact']);
});
