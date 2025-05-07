<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerAdController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DocumentRequirementController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// add api auth routes group
Route::middleware('auth:api')->group(function () {
    // create route group for chats
    Route::group(['prefix' => 'chats'], function () {
        Route::get('/', [ChatController::class, 'index']);
        Route::post('/send-message', [ChatController::class, 'sendUserMessage']);
    });
});

Route::group(['prefix' => 'admin', 'middleware' => 'auth:api'],function () {
    Route::group(['prefix' => 'chats', 'middleware'=>'permission:manage chats'], function () {
        Route::get('/', [ChatController::class, 'allData']);
        Route::get('/user/{user_id}', [ChatController::class, 'index']);
        Route::post('/send-message', [ChatController::class, 'sendAdminMessage']);
    });
    
    Route::group(['prefix' => 'users', 'middleware'=>'permission:manage users'], function () {
        Route::get('/', [UserController::class, 'allData']);
        Route::get('/user/{user_id}', [UserController::class, 'index']);
        Route::post('/create-user', [UserController::class, 'createUser']);
    });
});


Route::resource('areas', AreaController::class);
Route::resource('locations', LocationController::class);
Route::resource('banner-ads', BannerAdController::class);
Route::resource('invoices', InvoiceController::class);
Route::resource('notifications', NotificationController::class);
// Route::get('notifications', [NotificationController::class, 'index']);
Route::resource('document-requirements', DocumentRequirementController::class);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);