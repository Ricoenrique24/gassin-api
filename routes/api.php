<?php

use App\Http\Controllers\API\Employee\TransactionController;
use App\Http\Controllers\API\Manager\ResupplyTransactionController;
use App\Http\Controllers\API\Manager\PurchaseTransactionController;
use App\Http\Controllers\API\Manager\EmployeeController;
use App\Http\Controllers\API\Manager\CustomerController;
use App\Http\Controllers\API\Manager\StoreController;
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
    Route::apiResource('store', StoreController::class);
    Route::get('stores/search', [StoreController::class, 'search']);
    Route::apiResource('customer', CustomerController::class);
    Route::get('customers/search', [CustomerController::class, 'search']);
    Route::apiResource('employee', EmployeeController::class);
    Route::get('employees/search', [EmployeeController::class, 'search']);
    Route::apiResource('purchase', PurchaseTransactionController::class);
    Route::get('purchases/search', [PurchaseTransactionController::class, 'search']);
    Route::apiResource('resupply', ResupplyTransactionController::class);
    Route::get('resupplys/search', [ResupplyTransactionController::class, 'search']);
});

Route::middleware(['auth.apikey', 'role:employee'])->prefix('employee')->group(function () {
    Route::apiResource('transaction', TransactionController::class);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

