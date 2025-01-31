<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VaulingProductController;
use App\Http\Controllers\ProductsController;
use App\Http\Middleware\isAdmin;
use App\Http\Middleware\isUserAuth;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Public routes

// --- Auth routes
Route::post('/auth/sign_in', [AuthController::class, 'signIn']);
Route::post('/auth/sign_up', [AuthController::class, 'signUp']);
Route::get('/v1/products', [ProductsController::class, 'getProducts']);

// --- Product routes TO DO:

// Private routes
Route::middleware([isUserAuth::class])->group(function () {
    Route::get('/auth/me', [AuthController::class, 'getUser']);
    Route::post('/auth/sign_out', [AuthController::class, 'signOut']);

    // Vauling Product routes
    Route::post('/v1/vauling_product', [VaulingProductController::class, 'rateProduct']);
    Route::get('/v1/vauling_product/average/{id}', [VaulingProductController::class, 'getAverageRating']);
    Route::get('/v1/vauling_product/bestProduct', [VaulingProductController::class, 'getBestProduct']);
});

// Private routes
Route::middleware([isAdmin::class])->group(function () {
    Route::get('/v1/admin/say_hello', function () {
        return response()->json(['message' => 'Hello Admin!'], 200);
    });
    // TO DO
    Route::post('/v1/admin/products', [ProductsController::class, 'addProduct']);
    Route::get('/v1/admin/products/{id}', [ProductsController::class, 'getProductById']);
    Route::patch('/v1/admin/products/{id}', [ProductsController::class, 'updateProductById']);
    Route::delete('/v1/admin/products/{id}', [ProductsController::class, 'deleteProductById']);
});

Route::fallback(function () {
    return response()->json(['message' => 'Endpoint not found'], 404);
});
