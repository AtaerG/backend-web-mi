<?php

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);

Route::controller(ProductController::class)->group(function () {
    Route::get('products', 'index');
    Route::get('products/{product}', 'show');
    Route::patch('products/{product}','updateAmount');
});

Route::controller(CommentController::class)->group(function () {
    Route::get('comments', 'index');
    Route::get('comments/{comment}', 'show');
});



Route::middleware('auth:api','role')->group(function () {
    Route::get('logout', [PassportAuthController::class, 'logout']);
    Route::middleware(['scope:admin'])->group(function () {
        Route::controller(ProductController::class)->group(function () {
            Route::post('products', 'store');
            Route::put('products/{product}', 'update');
            Route::delete('products/{product}', 'destroy');
        });
        /*
        Route::controller(UserController::class)->group(function () {
            Route::get('users', 'index');
            Route::post('products', 'store');
            Route::put('products/{product}', 'update');
            Route::delete('products/{product}', 'destroy');
        });*/
    });
    Route::get('users/admins', [UserController::class, 'getOnlyAdminsIdForChatting']);
    Route::middleware(['scope:normal_user'])->group(function () {
        Route::post('messages', [ChatController::class, 'message']);
    });
    Route::controller(CommentController::class)->group(function () {
        Route::post('comments', 'store');
        Route::put('comments/{comment}', 'update');
        Route::delete('comments/{comment}', 'destroy');
    });
    Route::apiResource('users', UserController::class);
    Route::apiResource('orders', OrderController::class);
});

