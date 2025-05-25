<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerAdController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ConstantController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentRequirementController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertyServiceTypeController;
use App\Http\Controllers\PropertyTypeController;
use App\Http\Controllers\ServicePricingController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValuationRequestController;
use App\Models\PropertyServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::post('constants', [ConstantController::class, 'getData']);
Route::post('settings', [ConstantController::class, 'getSettingValue']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'chats'], function () {
        Route::get('/', [ChatController::class, 'index']);
        Route::post('send-message', [ChatController::class, 'sendUserMessage']);
    });

    Route::post('create-valuation-request', [ValuationRequestController::class, 'store']);
    Route::post('upload-valuation-documents', [ValuationRequestController::class, 'uploadDocuments']);

    Route::get('/user/{id}', [UserController::class, 'index']);
    
    Route::group(['prefix' => 'admin'],function () {
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
            Route::get('get/{id}', [BannerAdController::class, 'show']);
            Route::post('create', [BannerAdController::class, 'store']);
            Route::post('update/{id}', [BannerAdController::class, 'update']);
            Route::post('delete/{id}', [BannerAdController::class, 'delete']);
        });
        
        Route::group(['prefix' => 'companies', 'middleware'=>'permission:manage companies'], function () {
            Route::post('/', [CompanyController::class, 'getData']);
            Route::get('get/{id}', [CompanyController::class, 'show']);
            Route::post('create', [CompanyController::class, 'store']);
            Route::post('export', [CompanyController::class, 'export']);
            Route::post('update/{id}', [CompanyController::class, 'update']);
            Route::post('delete/{id}', [CompanyController::class, 'delete']);
        });
        
        Route::group(['prefix' => 'settings', 'middleware'=>'permission:manage settings'], function () {
            Route::get('/', [SettingController::class, 'getData']);
            Route::get('get/{key}', [SettingController::class, 'show']);
            Route::post('create', [SettingController::class, 'store']);
            Route::post('upload-image', [SettingController::class, 'uploadImage']);
            Route::post('update/{key}', [SettingController::class, 'update']);
            Route::post('delete/{key}', [SettingController::class, 'delete']);
        });

        Route::group(['prefix' => 'property-types', 'middleware'=>'permission:manage property-types'], function () {
            Route::get('/', [PropertyTypeController::class, 'getData']);
            Route::get('get/{id}', [PropertyTypeController::class, 'show']);
            Route::post('create', [PropertyTypeController::class, 'store']);
            Route::post('update/{id}', [PropertyTypeController::class, 'update']);
            Route::post('delete/{id}', [PropertyTypeController::class, 'delete']);
        });

        Route::group(['prefix' => 'service-types', 'middleware'=>'permission:manage service-types'], function () {
            Route::get('/', [ServiceTypeController::class, 'get']);
            Route::post('create', [ServiceTypeController::class, 'create']);
            Route::post('update/{id}', [ServiceTypeController::class, 'update']);
            Route::post('delete/{id}', [ServiceTypeController::class, 'delete']);
        });
        
        Route::group(['prefix' => 'property-service-types', 'middleware'=>'permission:manage property-service-types'], function () {
            Route::get('/', [PropertyServiceTypeController::class, 'getData']);
            Route::get('get/{property_type_id}', [PropertyServiceTypeController::class, 'show']);
            Route::post('create', [PropertyServiceTypeController::class, 'store']);
            Route::post('upload-image', [PropertyServiceTypeController::class, 'uploadImage']);
            Route::post('update/{key}', [PropertyServiceTypeController::class, 'update']);
            Route::post('delete/{key}', [PropertyServiceTypeController::class, 'delete']);
        });
        
        Route::group(['prefix' => 'service-pricing', 'middleware'=>'permission:manage service-pricing'], function () {
            Route::get('/', [ServicePricingController::class, 'getData']);
            Route::get('get/{property_type_id}', [ServicePricingController::class, 'show']);
            Route::post('create', [ServicePricingController::class, 'store']);
            Route::post('upload-image', [ServicePricingController::class, 'uploadImage']);
            Route::post('update/{key}', [ServicePricingController::class, 'update']);
            Route::post('delete/{key}', [ServicePricingController::class, 'delete']);
        });
        
        Route::group(['prefix' => 'document-requirements', 'middleware'=>'permission:manage document-requirements'], function () {
            Route::get('/', [DocumentRequirementController::class, 'getData']);
            Route::post('create', [DocumentRequirementController::class, 'store']);
            Route::post('update/{id}', [DocumentRequirementController::class, 'update']);
            Route::post('delete/{id}', [DocumentRequirementController::class, 'delete']);
        });
        
        Route::group(['prefix' => 'valuation-requests', 'middleware'=>'permission:manage valuation-requests'], function () {
            Route::post('/', [ValuationRequestController::class, 'getData']);
            Route::post('get/{request_id}', [ValuationRequestController::class, 'show']);
            Route::post('create', [ValuationRequestController::class, 'store']);
            Route::post('export', [ValuationRequestController::class, 'export']);
            Route::post('upload-documents', [ValuationRequestController::class, 'uploadDocuments']);
            Route::post('view-documents', [ValuationRequestController::class, 'viewDocuments']);
            Route::post('update/{id}', [ValuationRequestController::class, 'update']);
            Route::post('update-status', [ValuationRequestController::class, 'updateStatus']);
            Route::post('delete/{id}', [ValuationRequestController::class, 'delete']);
        });
        
        Route::group(['prefix' => 'payments', 'middleware'=>'permission:manage payments'], function () {
            Route::get('/', [PaymentController::class, 'getData']);
        });

    });
});

Route::get('/create-thawani-checkout', [PaymentController::class, 'createThawaniCheckout']);
Route::get('/cancel', function(){
    return response()->json([
        'status' => false,
        'message' => 'Payment cancelled',
    ]);
});
Route::get('/success', function(){
    return response()->json([
        'status' => true,
        'message' => 'Payment successful',
    ]);
});


// Route::get('areas', AreaController::class);
// Route::get('locations', LocationController::class);
// Route::get('banner-ads', BannerAdController::class);
// Route::get('invoices', InvoiceController::class);
// Route::get('notifications', NotificationController::class);
// Route::get('document-requirements', DocumentController::class);


