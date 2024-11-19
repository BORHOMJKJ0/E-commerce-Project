<?php

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Cart\CartItemsController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\Contact\ContactInformationController;
use App\Http\Controllers\Contact\ContactTypeController;
use App\Http\Controllers\Expression\ExpressionController;
use App\Http\Controllers\Image\ImageController;
use App\Http\Controllers\Offer\OfferController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemsController;
use App\Http\Controllers\Product\FavoriteProductController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Review\ReviewController;
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
    Route::apiResource('reviews', ReviewController::class);
    Route::apiResource('comments', CommentController::class);
    Route::apiResource('images', ImageController::class);
    Route::apiResource('cart_items', CartItemsController::class);
    Route::prefix('products')->controller(ProductController::class)->group(function () {
        Route::get('/order/{column}/{direction}', 'orderBy');
        Route::get('/my/order/{column}/{direction}', 'MyProductsOrderBy');
        Route::get('/my/random', 'MyProducts');
        Route::post('/search', 'searchByFilters');
    });
    Route::prefix('categories')->controller(CategoryController::class)->group(function () {
        Route::get('/order/{column}/{direction}', 'orderBy');
        Route::get('/my/order/{column}/{direction}', 'MyCategoriesOrderBy');
        Route::get('/my/random', 'MyCategories');
    });
    Route::prefix('products/favorites')->controller(FavoriteProductController::class)->group(function () {
        Route::get('/index', 'index');
        Route::post('/store/{product}', 'store')->missing(function () {
            return ResponseHelper::jsonResponse([], 'Product Not Found', 404, false);
        });
        Route::delete('/destroy/{product}', 'destroy')->missing(function () {
            return ResponseHelper::jsonResponse([], 'Product Not Found', 404, false);
        });
    });
    Route::post('product/create_product_with_all_details', [ProductController::class, 'create_product_with_details']);
    Route::get('warehouse/get_warehouse_have_offers', [WarehouseController::class, 'getWarehousesHaveOffers']);
    Route::prefix('warehouses')->controller(WarehouseController::class)->group(function () {
        Route::get('/order/{column}/{direction}', 'orderBy');
    });
    Route::prefix('offers')->controller(OfferController::class)->group(function () {
        Route::get('/order/{column}/{direction}', 'orderBy');
        Route::get('/my/order/{column}/{direction}', 'MyOffersOrderBy');
        Route::get('/my/random', 'MyOffers');
    });
    Route::prefix('reviews')->controller(ReviewController::class)->group(function () {
        Route::get('/order/{column}/{direction}', 'orderBy');
        Route::get('/my/order/{column}/{direction}', 'MyReviewsOrderBy');
        Route::get('/my/random', 'MyReviews');
    });
    Route::prefix('comments')->controller(CommentController::class)->group(function () {
        Route::get('/order/{column}/{direction}', 'orderBy');
        Route::get('/my/order/{column}/{direction}', 'MyCommentsOrderBy');
        Route::get('/my/random', 'MyComments');
    });
    Route::prefix('images')->controller(ImageController::class)->group(function () {
        Route::get('/order/{column}/{direction}', 'orderBy');
        Route::get('/my/order/{column}/{direction}', 'MyImagesOrderBy');
        Route::get('/my/random', 'MyImages');
        Route::post('/search', 'searchByFilters');
        Route::post('/update/{image}', 'update');
    });
    Route::prefix('carts')->controller(CartController::class)->group(function () {
        Route::get('/', 'show');
        Route::post('/', 'store');
        Route::put('/', 'update');
        Route::delete('/', 'destroy');
    });
    Route::prefix('cart_items')->controller(CartItemsController::class)->group(function () {
        Route::get('/order/{column}/{direction}', 'orderBy');
        Route::post('/search', 'searchByFilters');
    });
    Route::prefix('order_items')->controller(OrderItemsController::class)->group(function () {
        Route::get('place', 'placeOrder');
        Route::get('myOrder', 'myOrderItems');
    });
    Route::prefix('orders')->controller(OrderController::class)->group(function () {
        Route::delete('delete/{order}', 'delete')->missing(function () {
            return ResponseHelper::jsonResponse([], 'Order Not Found', 404, false);
        });
        Route::get('myOrders', 'myOrders');
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
        Route::post('store/fcm_token', 'storeFcmToken');
        Route::get('profile/{user}', 'profile')->whereNumber('user');
        Route::put('update/{user_id}', 'update')->whereNumber('user');
        Route::delete('destroy', 'destroy');
    });

    Route::controller(EmailVerificationController::class)->group(function () {
        Route::get('email-verification/{user:email}', 'sendEmailVerification')
            ->missing(function () {
                return ResponseHelper::jsonResponse([], 'Email Not Found', 404, false);
            });
        Route::post('email-verification', 'email_verification');
    });

    Route::get('password/forget-password/{user:email}', [ForgetPasswordController::class, 'forgetPassword'])
        ->missing(function () {
            return ResponseHelper::jsonResponse([], 'Email Not Found', 404, false);
        });
    Route::post('password/reset', [ResetPasswordController::class, 'resetPassword']);

    Route::controller(ContactInformationController::class)->prefix('contact')->group(function () {
        Route::post('add', 'store');
        Route::get('show/{user}', 'show')->missing(function () {
            return ResponseHelper::jsonResponse([], 'User Not Found', 404, false);
        });
        Route::put('update/{user_id}/{contact_id}', 'update');
        Route::delete('remove/{contact_information_id}', 'destroy')->whereNumber('contact_information_id');
        Route::delete('remove-all', 'destroyAll');
    });

    Route::controller(ContactTypeController::class)->group(function () {
        Route::get('contact-type-all', 'index');
        Route::get('contact-type/{id}', 'show')->whereNumber('id');
    });

    Route::controller(ExpressionController::class)->prefix('expression')->group(function () {
        Route::post('add', 'create');
        Route::get('all', 'index');
        Route::get('show/{product}', 'show')
            ->missing(function () {
                return ResponseHelper::jsonResponse([], 'Product Not Found', 404, false);
            });
        Route::put('update/{product}', 'update')
            ->missing(function () {
                return ResponseHelper::jsonResponse([], 'Product Not Found', 404, false);
            });
    });
});
