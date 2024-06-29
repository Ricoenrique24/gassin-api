<?php

use App\Http\Controllers\API\Employee\OperationTransactionController as EmployeeOperationTransactionController;
use App\Http\Controllers\API\Employee\TransactionController as EmployeeTransactionController;
use App\Http\Controllers\API\Manager\OperationTransactionController as ManagerOperationTransactionController;
use App\Http\Controllers\API\Manager\TransactionController as ManagerTransactionController;
use App\Http\Controllers\API\Manager\ResupplyTransactionController as ManagerResupplyTransactionController;
use App\Http\Controllers\API\Manager\PurchaseTransactionController as ManagerPurchaseTransactionController;
use App\Http\Controllers\API\Manager\EmployeeController as ManagerEmployeeController;
use App\Http\Controllers\API\Manager\CustomerController as ManagerCustomerController;
use App\Http\Controllers\API\Manager\StoreController as ManagerStoreController;
use App\Http\Controllers\API\Manager\DashboardController as ManagerDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthController;
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

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth.apikey', 'role:manager'])->prefix('manager')->group(function () {
    Route::apiResource('store', ManagerStoreController::class);
    Route::get('stores/search', [ManagerStoreController::class, 'search']);
    Route::apiResource('customer', ManagerCustomerController::class);
    Route::get('customers/search', [ManagerCustomerController::class, 'search']);
    Route::apiResource('employee', ManagerEmployeeController::class);
    Route::get('employees/search', [ManagerEmployeeController::class, 'search']);
    Route::apiResource('purchase', ManagerPurchaseTransactionController::class);
    Route::prefix('purchases')->group(function () {
        Route::get('search', [ManagerPurchaseTransactionController::class, 'search']);
        Route::get('filter', [ManagerPurchaseTransactionController::class, 'filter']);
    });
    Route::apiResource('resupply', ManagerResupplyTransactionController::class);
    Route::prefix('resupplys')->group(function () {
        Route::get('search', [ManagerResupplyTransactionController::class, 'search']);
        Route::get('filter', [ManagerResupplyTransactionController::class, 'filter']);
    });
    Route::prefix('operation')->group(function () {
        Route::get('/', [ManagerOperationTransactionController::class, 'index']);
        Route::get('/{id}', [ManagerOperationTransactionController::class, 'show']);
        Route::put('/{id}', [ManagerOperationTransactionController::class, 'update']);
    });
    Route::get('operations/search', [ManagerOperationTransactionController::class, 'search']);
    Route::apiResource('transaction', ManagerTransactionController::class);
    Route::prefix('dashboard')->group(function () {
        Route::get('getAvailableStockQuantity', [ManagerDashboardController::class, 'getAvailableStockQuantity']);
        Route::get('getRevenueToday', [ManagerDashboardController::class, 'getRevenueToday']);
    });
});

Route::middleware(['auth.apikey', 'role:employee'])->prefix('employee')->group(function () {
    Route::prefix('transaction')->group(function () {
        Route::get('/', [EmployeeTransactionController::class, 'index']);
        Route::get('/{id}', [EmployeeTransactionController::class, 'show']);
        Route::get('active', [EmployeeTransactionController::class, 'active']);
        Route::get('inProgress/{id}', [EmployeeTransactionController::class, 'inProgress']);
        Route::get('completed/{id}', [EmployeeTransactionController::class, 'completed']);
        Route::get('cancelled/{id}', [EmployeeTransactionController::class, 'cancelled']);
    });
    Route::prefix('operation')->group(function () {
        Route::post('/', [EmployeeOperationTransactionController::class, 'store']);
        Route::get('/{id}', [EmployeeOperationTransactionController::class, 'show']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

