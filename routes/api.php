<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerAdController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DocumentRequirementController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PropertyTypeController;
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
        Route::post('send-message', [ChatController::class, 'sendUserMessage']);
    });

    Route::get('/user/{id}', [UserController::class, 'index']);
});

Route::group(['prefix' => 'admin', 'middleware' => 'auth:api'],function () {
    Route::group(['prefix' => 'chats', 'middleware'=>'permission:manage chats'], function () {
        Route::get('/', [ChatController::class, 'getData']);
        Route::get('get/{id}', [ChatController::class, 'index']);
        Route::post('send-message', [ChatController::class, 'sendAdminMessage']);
    });
    
    Route::group(['prefix' => 'users', 'middleware'=>'permission:manage users'], function () {
        Route::get('/', [UserController::class, 'getData']);
        Route::get('get/{id}', [UserController::class, 'index']);
        Route::post('create', [UserController::class, 'store']);
        Route::post('update/{id}', [UserController::class, 'update']);
        Route::post('delete/{id}', [UserController::class, 'delete']);
        Route::post('update-password/{id}', [UserController::class, 'updatePassword']);
    });
    
    Route::group(['prefix' => 'locations', 'middleware'=>'permission:manage locations'], function () {
        Route::get('/', [LocationController::class, 'getData']);
        Route::get('get/{id}', [LocationController::class, 'index']);
        Route::post('create', [LocationController::class, 'store']);
        Route::post('update/{id}', [LocationController::class, 'update']);
        Route::post('delete/{id}', [LocationController::class, 'delete']);
    });

    Route::group(['prefix' => 'banner-ads', 'middleware'=>'permission:manage banner ads'], function () {
        Route::get('/', [BannerAdController::class, 'getData']);
        Route::get('get/{id}', [BannerAdController::class, 'index']);
        Route::post('create', [BannerAdController::class, 'store']);
        Route::post('update/{id}', [BannerAdController::class, 'update']);
        Route::post('delete/{id}', [BannerAdController::class, 'delete']);
    });
    
    Route::group(['prefix' => 'property-types', 'middleware'=>'permission:manage property-types'], function () {
        Route::get('/', [PropertyTypeController::class, 'getData']);
        Route::get('get/{id}', [PropertyTypeController::class, 'show']);
        Route::post('create', [PropertyTypeController::class, 'store']);
        Route::post('update/{id}', [PropertyTypeController::class, 'update']);
        Route::post('delete/{id}', [PropertyTypeController::class, 'delete']);
    });
});

Route::get('areas', AreaController::class);
Route::get('locations', LocationController::class);
Route::get('banner-ads', BannerAdController::class);
Route::get('invoices', InvoiceController::class);
Route::get('notifications', NotificationController::class);
Route::get('document-requirements', DocumentRequirementController::class);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');