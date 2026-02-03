<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Inventory\InventoryController;
use App\Http\Controllers\Api\CRM\CustomerController;
use App\Http\Controllers\Api\CRM\ContactController;
use App\Http\Controllers\Api\CRM\LeadController;
use App\Http\Controllers\Api\Procurement\VendorController;
use App\Http\Controllers\Api\Procurement\PurchaseOrderController;
use App\Http\Controllers\Api\Procurement\PurchaseReceiptController;
use App\Http\Controllers\Api\Procurement\PurchaseReturnController;
use App\Http\Controllers\Api\Sales\QuoteController;
use App\Http\Controllers\Api\Sales\SalesOrderController;
use App\Http\Controllers\Api\Invoice\InvoiceController;
use App\Http\Controllers\Api\Payment\PaymentController;
use App\Http\Controllers\Api\POS\POSSessionController;
use App\Http\Controllers\Api\POS\POSTransactionController;
use App\Http\Controllers\Api\IAM\UserController;
use App\Http\Controllers\Api\IAM\RoleController;
use App\Http\Controllers\Api\IAM\PermissionController;

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
    
    // Authentication routes (public)
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });
});

// Protected routes (authentication required)
Route::prefix('v1')->middleware(['auth:sanctum', 'tenant.context'])->group(function () {
    
    // Authentication routes (protected)
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
    
    // IAM Routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/search', [UserController::class, 'search']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::post('/{id}/restore', [UserController::class, 'restore']);
        Route::post('/{id}/roles', [UserController::class, 'assignRoles']);
    });

    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::get('/{id}', [RoleController::class, 'show']);
        Route::put('/{id}', [RoleController::class, 'update']);
        Route::delete('/{id}', [RoleController::class, 'destroy']);
        Route::post('/{id}/permissions', [RoleController::class, 'assignPermissions']);
    });

    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index']);
        Route::post('/', [PermissionController::class, 'store']);
        Route::get('/by-module', [PermissionController::class, 'byModule']);
        Route::get('/{id}', [PermissionController::class, 'show']);
        Route::put('/{id}', [PermissionController::class, 'update']);
        Route::delete('/{id}', [PermissionController::class, 'destroy']);
    });
    
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

    // CRM Routes
    Route::prefix('crm')->group(function () {
        // Customer routes
        Route::prefix('customers')->group(function () {
            Route::get('/', [CustomerController::class, 'index']);
            Route::post('/', [CustomerController::class, 'store']);
            Route::get('/search', [CustomerController::class, 'search']);
            Route::get('/statistics', [CustomerController::class, 'statistics']);
            Route::get('/{id}', [CustomerController::class, 'show']);
            Route::put('/{id}', [CustomerController::class, 'update']);
            Route::delete('/{id}', [CustomerController::class, 'destroy']);
        });

        // Contact routes
        Route::prefix('contacts')->group(function () {
            Route::get('/', [ContactController::class, 'index']);
            Route::post('/', [ContactController::class, 'store']);
            Route::get('/search', [ContactController::class, 'search']);
            Route::get('/{id}', [ContactController::class, 'show']);
            Route::put('/{id}', [ContactController::class, 'update']);
            Route::delete('/{id}', [ContactController::class, 'destroy']);
        });

        // Lead routes
        Route::prefix('leads')->group(function () {
            Route::get('/', [LeadController::class, 'index']);
            Route::post('/', [LeadController::class, 'store']);
            Route::get('/search', [LeadController::class, 'search']);
            Route::get('/statistics', [LeadController::class, 'statistics']);
            Route::get('/{id}', [LeadController::class, 'show']);
            Route::put('/{id}', [LeadController::class, 'update']);
            Route::delete('/{id}', [LeadController::class, 'destroy']);
            Route::post('/{id}/convert', [LeadController::class, 'convert']);
        });
    });

    // Procurement Routes
    Route::prefix('procurement')->group(function () {
        // Vendor routes
        Route::prefix('vendors')->group(function () {
            Route::get('/', [VendorController::class, 'index']);
            Route::post('/', [VendorController::class, 'store']);
            Route::get('/search', [VendorController::class, 'search']);
            Route::get('/statistics', [VendorController::class, 'statistics']);
            Route::get('/{id}', [VendorController::class, 'show']);
            Route::put('/{id}', [VendorController::class, 'update']);
            Route::delete('/{id}', [VendorController::class, 'destroy']);
        });

        // Purchase Order routes
        Route::prefix('purchase-orders')->group(function () {
            Route::get('/', [PurchaseOrderController::class, 'index']);
            Route::post('/', [PurchaseOrderController::class, 'store']);
            Route::get('/{id}', [PurchaseOrderController::class, 'show']);
            Route::put('/{id}', [PurchaseOrderController::class, 'update']);
            Route::delete('/{id}', [PurchaseOrderController::class, 'destroy']);
            Route::post('/{id}/approve', [PurchaseOrderController::class, 'approve']);
            Route::post('/{id}/reject', [PurchaseOrderController::class, 'reject']);
            Route::post('/{id}/cancel', [PurchaseOrderController::class, 'cancel']);
        });

        // Purchase Receipt routes (GRN)
        Route::prefix('purchase-receipts')->group(function () {
            Route::get('/', [PurchaseReceiptController::class, 'index']);
            Route::post('/', [PurchaseReceiptController::class, 'store']);
            Route::get('/{id}', [PurchaseReceiptController::class, 'show']);
            Route::delete('/{id}', [PurchaseReceiptController::class, 'destroy']);
            Route::post('/{id}/accept', [PurchaseReceiptController::class, 'accept']);
        });

        // Purchase Return routes
        Route::prefix('purchase-returns')->group(function () {
            Route::get('/', [PurchaseReturnController::class, 'index']);
            Route::post('/', [PurchaseReturnController::class, 'store']);
            Route::get('/{id}', [PurchaseReturnController::class, 'show']);
            Route::delete('/{id}', [PurchaseReturnController::class, 'destroy']);
            Route::post('/{id}/approve', [PurchaseReturnController::class, 'approve']);
        });
    });

    // Sales Routes
    Route::prefix('sales')->group(function () {
        // Quote routes
        Route::prefix('quotes')->group(function () {
            Route::get('/', [QuoteController::class, 'index']);
            Route::post('/', [QuoteController::class, 'store']);
            Route::get('/{id}', [QuoteController::class, 'show']);
            Route::put('/{id}', [QuoteController::class, 'update']);
            Route::delete('/{id}', [QuoteController::class, 'destroy']);
            Route::post('/{id}/convert', [QuoteController::class, 'convertToOrder']);
        });

        // Sales Order routes
        Route::prefix('orders')->group(function () {
            Route::get('/', [SalesOrderController::class, 'index']);
            Route::post('/', [SalesOrderController::class, 'store']);
            Route::get('/{id}', [SalesOrderController::class, 'show']);
            Route::put('/{id}', [SalesOrderController::class, 'update']);
            Route::delete('/{id}', [SalesOrderController::class, 'destroy']);
            Route::post('/{id}/approve', [SalesOrderController::class, 'approve']);
            Route::post('/{id}/reserve-inventory', [SalesOrderController::class, 'reserveInventory']);
            Route::post('/from-quote/{quoteId}', [SalesOrderController::class, 'createFromQuote']);
        });
    });

    // Invoice Routes
    Route::prefix('invoices')->group(function () {
        Route::get('/', [InvoiceController::class, 'index']);
        Route::post('/', [InvoiceController::class, 'store']);
        Route::get('/{id}', [InvoiceController::class, 'show']);
        Route::put('/{id}', [InvoiceController::class, 'update']);
        Route::delete('/{id}', [InvoiceController::class, 'destroy']);
        Route::post('/{id}/approve', [InvoiceController::class, 'approve']);
        Route::post('/{id}/payments', [InvoiceController::class, 'recordPayment']);
        Route::post('/from-sales-order/{salesOrderId}', [InvoiceController::class, 'createFromSalesOrder']);
    });
    
    // Payment Management Routes
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index']);
        Route::post('/', [PaymentController::class, 'store']);
        Route::get('/search', [PaymentController::class, 'search']);
        Route::get('/statistics', [PaymentController::class, 'statistics']);
        Route::get('/{id}', [PaymentController::class, 'show']);
        Route::put('/{id}', [PaymentController::class, 'update']);
        Route::delete('/{id}', [PaymentController::class, 'destroy']);
        Route::post('/{id}/reconcile', [PaymentController::class, 'reconcile']);
        Route::post('/{id}/unreconcile', [PaymentController::class, 'unreconcile']);
        Route::post('/{id}/complete', [PaymentController::class, 'complete']);
        Route::post('/{id}/cancel', [PaymentController::class, 'cancel']);
    });
    
    // POS Management Routes
    Route::prefix('pos')->group(function () {
        // POS Sessions
        Route::prefix('sessions')->group(function () {
            Route::get('/', [POSSessionController::class, 'index']);
            Route::post('/', [POSSessionController::class, 'store']);
            Route::get('/current', [POSSessionController::class, 'current']);
            Route::get('/{id}', [POSSessionController::class, 'show']);
            Route::post('/{id}/close', [POSSessionController::class, 'close']);
        });
        
        // POS Transactions
        Route::prefix('transactions')->group(function () {
            Route::get('/', [POSTransactionController::class, 'index']);
            Route::post('/', [POSTransactionController::class, 'store']);
            Route::get('/{id}', [POSTransactionController::class, 'show']);
            Route::post('/{id}/complete', [POSTransactionController::class, 'complete']);
            Route::post('/{id}/receipt', [POSTransactionController::class, 'generateReceipt']);
        });
    });
});
