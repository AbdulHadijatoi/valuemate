<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerAdController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DocumentRequirementController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// add api auth routes group
Route::middleware('auth')->group(function () {
    Route::resource('chats', ChatController::class);
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