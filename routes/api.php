<?php

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\GoogleV3CaptchaController;
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
});

Route::controller(CommentController::class)->group(function () {
    Route::get('comments', 'index');
    Route::get('comments/{comment}', 'show');
});


Route::controller(ProductController::class)->group(function () {
    Route::put('products/{product}', 'update');
    Route::get('products/amount/{product}', 'getAmountOfProduct');
});

Route::middleware('auth:api','role')->group(function () {
    Route::post('password/forgot', [PasswordController::class, 'forgot']);
    Route::post('password/reset', [PasswordController::class, 'reset']);

    Route::post('messages', [ChatController::class, 'sendMessage']);

    Route::get('logout', [PassportAuthController::class, 'logout']);
    Route::middleware(['scope:admin'])->group(function () {
        Route::controller(ProductController::class)->group(function () {
            Route::post('products', 'store');
            Route::patch('products/{product}', 'deleteProduct');
        });
    });
    Route::get('users/admins', [UserController::class, 'getOnlyAdminsIdForChatting']);
    Route::controller(CommentController::class)->group(function () {
        Route::post('comments', 'store');
        Route::put('comments/{comment}', 'update');
        Route::delete('comments/{comment}', 'destroy');
    });
    Route::controller(UserController::class)->group(function () {
        Route::get('users-admins', 'getAdmins');
    });
    Route::controller(OrderController::class)->group(function () {
        Route::get('orders', 'index');
        Route::post('orders', 'store');
        Route::post('orders/user', 'getOrdersOfUser');
        Route::get('orders/{order}', 'show');
        Route::put('orders/{order}', 'update');
        Route::delete('orders/{order}', 'destroy');
    });
    Route::controller(AppointmentController::class)->group(function () {
        Route::get('appointments', 'index');
        Route::post('appointments', 'store');
        Route::delete('appointments/{appointment}', 'destroy');
        Route::post('appt-admin', 'getAdminsAppointments');
        Route::post('appt-user', 'getUsersAppointments');
    });
    Route::apiResource('users', UserController::class);
});

