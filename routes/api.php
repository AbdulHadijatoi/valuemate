<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('chats', ChatController::class);
Route::apiResource('areas', AreaController::class);
Route::apiResource('locations', LocationController::class);
Route::apiResource('banner-ads', BannerAdController::class);
Route::apiResource('invoices', InvoiceController::class);
Route::apiResource('notifications', NotificationController::class);
Route::apiResource('document-requirements', DocumentRequirementController::class);