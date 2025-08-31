<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerAdController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ConstantController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentRequirementController;
use App\Http\Controllers\GuidelineController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodController;
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

Route::post('success2', function(){
    return response()->json([
        "status" => true,
        "message" => 'Payment was successful and verified.'
    ],200);
});

Route::post('cancel2', function(){
    return response()->json([
        "status" => false,
        "message" => 'Payment was canceled.'
    ],422);
});

Route::get('success/{payment_reference}', [PaymentController::class, 'success']);
Route::get('cancel/{payment_reference}', [PaymentController::class, 'cancel']);
Route::get('checkout-test', [PaymentController::class, 'createThawaniCheckout']);

Route::post('terms', [GuidelineController::class, 'getTerms']);
Route::post('policy', [GuidelineController::class, 'getPrivacyPolicy']);

Route::middleware('auth:api')->group(function () {
    Route::post('checkout', [PaymentController::class, 'createThawaniCheckout']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'users'], function () {
        Route::post('update/{id}', [UserController::class, 'update']);
        Route::post('delete/{id}', [UserController::class, 'delete']);
    });

    Route::group(['prefix' => 'chats'], function () {
        Route::get('/', [ChatController::class, 'index']);
        Route::post('send-message', [ChatController::class, 'sendUserMessage']);
    });

    
    Route::post('request-history', [ValuationRequestController::class, 'requestHistory']);
    Route::post('create-valuation-request', [ValuationRequestController::class, 'store']);
    Route::post('upload-valuation-documents', [ValuationRequestController::class, 'uploadDocuments']);

    Route::get('/user/{id}', [UserController::class, 'index']);
    
    Route::group(['prefix' => 'admin'],function () {
        Route::group(['prefix' => 'chats', 'middleware'=>'permission:manage chats'], function () {
            Route::post('/', [ChatController::class, 'getData']);
            Route::get('get/{id}', [ChatController::class, 'index']);
            Route::post('send-message', [ChatController::class, 'sendAdminMessage']);

            Route::get('/chat-room', [ChatRoomController::class, 'getOrCreateRoom']);
            Route::post('/chat-message', [ChatRoomController::class, 'sendMessage']);
            Route::get('/chat-messages/{roomId}', [ChatRoomController::class, 'getMessages']);
        });
        
        Route::group(['prefix' => 'users', 'middleware'=>'permission:manage users'], function () {
            Route::post('/', [UserController::class, 'getData']);
            Route::get('get/{id}', [UserController::class, 'index']);
            Route::post('create', [UserController::class, 'store']);
            Route::post('export', [UserController::class, 'export']);
            Route::post('update/{id}', [UserController::class, 'update']);
            Route::post('delete/{id}', [UserController::class, 'delete']);
            Route::post('update-password/{id}', [UserController::class, 'updatePassword']);
        });
        
        Route::group(['prefix' => 'locations', 'middleware'=>'permission:manage locations'], function () {
            Route::post('/', [LocationController::class, 'getData']);
            Route::get('get/{id}', [LocationController::class, 'index']);
            Route::post('create', [LocationController::class, 'store']);
            Route::post('export', [LocationController::class, 'export']);
            Route::post('update/{id}', [LocationController::class, 'update']);
            Route::post('delete/{id}', [LocationController::class, 'delete']);
        });
    
        Route::group(['prefix' => 'banner-ads', 'middleware'=>'permission:manage banner ads'], function () {
            Route::post('/', [BannerAdController::class, 'getData']);
            Route::get('get/{id}', [BannerAdController::class, 'show']);
            Route::post('create', [BannerAdController::class, 'store']);
            Route::post('export', [BannerAdController::class, 'export']);
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
            Route::post('/', [SettingController::class, 'getData']);
            Route::get('get/{key}', [SettingController::class, 'show']);
            Route::post('create', [SettingController::class, 'store']);
            Route::post('upload-image', [SettingController::class, 'uploadImage']);
            Route::post('update/{key}', [SettingController::class, 'update']);
            Route::post('delete/{key}', [SettingController::class, 'delete']);
        });

        Route::group(['prefix' => 'property-types', 'middleware'=>'permission:manage property-types'], function () {
            Route::post('/', [PropertyTypeController::class, 'getData']);
            Route::get('get/{id}', [PropertyTypeController::class, 'show']);
            Route::post('create', [PropertyTypeController::class, 'store']);
            Route::post('export', [PropertyTypeController::class, 'export']);
            Route::post('update/{id}', [PropertyTypeController::class, 'update']);
            Route::post('delete/{id}', [PropertyTypeController::class, 'delete']);
        });

        Route::group(['prefix' => 'service-types', 'middleware'=>'permission:manage service-types'], function () {
            Route::post('/', [ServiceTypeController::class, 'getData']);
            Route::post('create', [ServiceTypeController::class, 'store']);
            Route::post('export', [ServiceTypeController::class, 'export']);
            Route::post('update/{id}', [ServiceTypeController::class, 'update']);
            Route::post('delete/{id}', [ServiceTypeController::class, 'delete']);
        });
        
        Route::group(['prefix' => 'property-service-types', 'middleware'=>'permission:manage property-service-types'], function () {
            Route::post('/', [PropertyServiceTypeController::class, 'getData']);
            Route::get('get/{property_type_id}', [PropertyServiceTypeController::class, 'show']);
            Route::post('create', [PropertyServiceTypeController::class, 'store']);
            Route::post('export', [PropertyServiceTypeController::class, 'export']);
            Route::post('upload-image', [PropertyServiceTypeController::class, 'uploadImage']);
            Route::post('update/{key}', [PropertyServiceTypeController::class, 'update']);
            Route::post('delete/{key}', [PropertyServiceTypeController::class, 'delete']);
        });
        
        Route::group(['prefix' => 'service-pricing', 'middleware'=>'permission:manage service-pricing'], function () {
            Route::post('/', [ServicePricingController::class, 'getData']);
            Route::post('create', [ServicePricingController::class, 'store']);
            Route::post('update/{id}', [ServicePricingController::class, 'update']);
            Route::post('delete/{id}', [ServicePricingController::class, 'delete']);
        });
        
        Route::group(['prefix' => 'document-requirements', 'middleware'=>'permission:manage document-requirements'], function () {
            Route::post('/', [DocumentRequirementController::class, 'getData']);
            Route::post('create', [DocumentRequirementController::class, 'store']);
            Route::post('export', [DocumentRequirementController::class, 'export']);
            Route::post('update/{id}', [DocumentRequirementController::class, 'update']);
            Route::post('delete/{id}', [DocumentRequirementController::class, 'delete']);
        });
        
        Route::group(['prefix' => 'payment-methods', 'middleware'=>'permission:manage payment-methods'], function () {
            Route::post('/', [PaymentMethodController::class, 'getData']);
            Route::post('create', [PaymentMethodController::class, 'store']);
            Route::post('update/{id}', [PaymentMethodController::class, 'update']);
            Route::post('delete/{id}', [PaymentMethodController::class, 'delete']);
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

        Route::group(['prefix' => 'guidelines', 'middleware'=>'permission:manage guidelines'], function () {
            Route::post('/', [GuidelineController::class, 'getData']);
            Route::get('terms', [GuidelineController::class, 'getTerms']);
            Route::get('privacy-policy', [GuidelineController::class, 'getPrivacyPolicy']);
            Route::post('store', [GuidelineController::class, 'storeGuideline']);
            Route::post('update/{id}', [GuidelineController::class, 'updateGuideline']);
        });
        
        Route::group(['prefix' => 'payments', 'middleware'=>'permission:manage payments'], function () {
            Route::post('/', [PaymentController::class, 'getData']);
        });

        Route::group(['prefix' => 'constants'], function(){
            Route::post('/', [ConstantController::class, 'constantData']);
        });

    });
});




// Route::get('areas', AreaController::class);
// Route::get('locations', LocationController::class);
// Route::get('banner-ads', BannerAdController::class);
// Route::get('invoices', InvoiceController::class);
// Route::get('notifications', NotificationController::class);
// Route::get('document-requirements', DocumentController::class);


