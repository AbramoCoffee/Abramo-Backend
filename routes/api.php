<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'api'], function ($router) {
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    //login api
    Route::post('/login', [AuthController::class, 'login']);

    //register api
    Route::post('/register', [AuthController::class, 'register']);

    //logout api
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    //categories api
    // Route::apiResource('/api-categories', CategoryController::class)->middleware('auth:sanctum');
    Route::get('/api-categories', [CategoryController::class, 'index'])->middleware('auth:sanctum');
    Route::post('/api-categories', [CategoryController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/api-categories/{id}', [CategoryController::class, 'show'])->middleware('auth:sanctum');
    Route::post('/api-categories/{id}', [CategoryController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/api-categories/{id}', [CategoryController::class, 'destroy'])->middleware('auth:sanctum');

    //products api
    // Route::apiResource('/api-products', ProductController::class)->middleware('auth:sanctum');
    Route::get('/api-products', [ProductController::class, 'index'])->middleware('auth:sanctum');
    Route::post('/api-products', [ProductController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/api-products/{id}', [ProductController::class, 'show'])->middleware('auth:sanctum');
    Route::post('/api-products/{id}', [ProductController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/api-products/{id}', [ProductController::class, 'destroy'])->middleware('auth:sanctum');
});
