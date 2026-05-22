<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SubscriptionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. MODULE SERVICES
// Otomatis mendaftarkan route index, store, show, update, destroy untuk services
Route::apiResource("services", ServiceController::class);
// Custom route untuk aktivasi dan deaktivasi status service
Route::patch("services/{service}/activate", [ServiceController::class, "activate"]);
Route::patch("services/{service}/deactivate", [ServiceController::class, "deactivate"]);


// 2. MODULE CUSTOMERS
// Otomatis mendaftarkan route index, store, show, update, destroy untuk customers
Route::apiResource("customers", CustomerController::class);


// 3. MODULE SUBSCRIPTIONS
// Otomatis mendaftarkan route index, store, show, update, destroy untuk subscriptions
Route::apiResource("subscriptions", SubscriptionController::class);