<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReportController;
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

    //Category API
    // Route::apiResource('/api-categories', CategoryController::class)->middleware('auth:sanctum');
    Route::get('/api-categories', [CategoryController::class, 'index'])->middleware('auth:sanctum');
    Route::post('/api-categories', [CategoryController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/api-categories/{id}', [CategoryController::class, 'show'])->middleware('auth:sanctum');
    Route::post('/api-categories/{id}', [CategoryController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/api-categories/{id}', [CategoryController::class, 'destroy'])->middleware('auth:sanctum');


    //Product API
    // Route::apiResource('/api-products', ProductController::class)->middleware('auth:sanctum');
    Route::get('/api-products', [ProductController::class, 'index'])->middleware('auth:sanctum');
    Route::post('/api-products', [ProductController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/api-products/{id}', [ProductController::class, 'show'])->middleware('auth:sanctum');
    Route::post('/api-products/{id}', [ProductController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/api-products/{id}', [ProductController::class, 'destroy'])->middleware('auth:sanctum');


    // Order API
    Route::get('/api-orders', [OrderController::class, 'getOrders'])->middleware('auth:sanctum');
    Route::post('/api-orders', [OrderController::class, 'createOrder'])->middleware('auth:sanctum');
    Route::get('/api-orders/{time}', [OrderController::class, 'getOrdersByTime'])->middleware('auth:sanctum');


    // OrderItem API
    Route::get('/api-order-item/{id}', [OrderItemController::class, 'getOrderItem'])->middleware('auth:sanctum');
    Route::get('/api-order-item', [OrderItemController::class, 'getOrderItems'])->middleware('auth:sanctum');
    Route::get('/api-order-item-by-order/{id}', [OrderItemController::class, 'getOrderItemsByOrder'])->middleware('auth:sanctum');

    // Report API
    // Report
    Route::get('/api-reports', [ReportController::class, 'index'])->middleware('auth:sanctum');
    Route::post('/api-reports', [ReportController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/api-reports/{id}', [ReportController::class, 'show'])->middleware('auth:sanctum');
    Route::post('/api-reports/{id}', [ReportController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/api-reports/{id}', [ReportController::class, 'destroy'])->middleware('auth:sanctum');
    Route::get('/api-reports-time/{time}', [ReportController::class, 'reportsByTime'])->middleware('auth:sanctum');
    Route::get('/api-reports-income/{time}', [ReportController::class, 'incomeReportByTime'])->middleware('auth:sanctum');
    Route::get('/api-reports-outcome/{time}', [ReportController::class, 'outcomeReportByTime'])->middleware('auth:sanctum');
    Route::get('/api-reports-revenue/{time}', [ReportController::class, 'revenueReportByTime'])->middleware('auth:sanctum');
});
