<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Inventory\InventoryController;

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

// Public routes (no authentication required)
Route::prefix('v1')->group(function () {
    // Health check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'version' => '1.0.0',
        ]);
    });
});

// Protected routes (authentication required)
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    
    // Product Management Routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::get('/search', [ProductController::class, 'search']);
        Route::get('/low-stock', [ProductController::class, 'lowStock']);
        Route::get('/out-of-stock', [ProductController::class, 'outOfStock']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
        Route::post('/{id}/calculate-price', [ProductController::class, 'calculatePrice']);
    });

    // Inventory Management Routes
    Route::prefix('inventory')->group(function () {
        // Stock movements
        Route::post('/stock-in', [InventoryController::class, 'stockIn']);
        Route::post('/stock-out', [InventoryController::class, 'stockOut']);
        Route::post('/adjustment', [InventoryController::class, 'stockAdjustment']);
        Route::post('/transfer', [InventoryController::class, 'stockTransfer']);
        
        // Stock queries
        Route::get('/balance', [InventoryController::class, 'getCurrentBalance']);
        Route::get('/movements', [InventoryController::class, 'getMovements']);
        Route::get('/expiring-items', [InventoryController::class, 'getExpiringItems']);
        Route::get('/valuation', [InventoryController::class, 'calculateValuation']);
    });
});
