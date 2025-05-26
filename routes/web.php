<?php

use App\Http\Controllers\ServiceTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('test', [ServiceTypeController::class, 'getData'])->name('service_type.get_data');