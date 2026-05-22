<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;

// Mendaftarkan route resource CRUD dan custom patch
Route::apiResource("services", ServiceController::class);

Route::patch("services/{service}/activate", [ServiceController::class, "activate"]);
Route::patch("services/{service}/deactivate", [ServiceController::class, "deactivate"]);