<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SubscriptionController;

Route::get('services/filter/status', [ServiceController::class, 'getByStatus']);
Route::patch('services/{service}/change-status', [ServiceController::class, 'changeStatus']);
Route::apiResource("services", ServiceController::class);

Route::get('customers/filter/status', [CustomerController::class, 'getByStatus']);
Route::patch('customers/{id}/change-status', [CustomerController::class, 'changeStatus']);
Route::apiResource("customers", CustomerController::class);

Route::get('subscriptions/filter/status', [SubscriptionController::class, 'getByStatus']);
Route::patch('subscriptions/{id}/change-status', [SubscriptionController::class, 'changeStatus']);
Route::apiResource("subscriptions", SubscriptionController::class);