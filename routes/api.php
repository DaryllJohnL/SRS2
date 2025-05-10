<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DepartmentUserRoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\RebateTypeController;
use App\Http\Controllers\ReferenceTypeController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\TransactionHeaderController;
use App\Http\Controllers\TransactionController;
// Public route for authentication
Route::post('login', [AuthController::class, 'login']); // User login

// Dashboard route (accessible only by authenticated users)
Route::middleware('auth:sanctum')->get('dashboard/stats', [DashboardController::class, 'getDashboardStats']);
Route::middleware('auth:sanctum')->get('/user-dashboard', [UserDashboardController::class, 'getUserDashboardStats']);
// User management routes
Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('{id}', [UserController::class, 'show']);
    Route::post('/', [UserController::class, 'store']);
    Route::put('{id}', [UserController::class, 'update']);
    Route::delete('{id}', [UserController::class, 'destroy']);

    // Corrected the route path to handle password change with middleware
    Route::put('{id}/change-password', [UserController::class, 'changePassword']);
});

// Department and role management routes
Route::prefix('departments')->middleware('auth:sanctum')->group(function () {
    Route::post('{department}/users/{user}/roles', [DepartmentUserRoleController::class, 'assignRoleToUser']);
    Route::get('{department}/users', [DepartmentUserRoleController::class, 'getUsersInDepartment']);
});

Route::apiResource('departments', DepartmentController::class)->middleware('auth:sanctum');
Route::apiResource('roles', RoleController::class)->middleware('auth:sanctum');

// Supplier management routes
Route::prefix('suppliers')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [SupplierController::class, 'index']);
    Route::post('/', [SupplierController::class, 'store']);
    Route::put('{id}', [SupplierController::class, 'update']);
    Route::patch('{id}/status', [SupplierController::class, 'updateStatus']);
    Route::post('upload', [SupplierController::class, 'uploadFile']);
});

// Rebate Type CRUD routes
Route::prefix('rebates')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [RebateTypeController::class, 'index']);
    Route::post('/', [RebateTypeController::class, 'store']);
    Route::get('{id}', [RebateTypeController::class, 'show']);
    Route::put('{id}', [RebateTypeController::class, 'update']);
    Route::delete('{id}', [RebateTypeController::class, 'destroy']);
});

// Reference Type CRUD routes
Route::prefix('reference-types')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ReferenceTypeController::class, 'index']);
    Route::post('/', [ReferenceTypeController::class, 'store']);
    Route::put('{id}', [ReferenceTypeController::class, 'update']);
    Route::delete('{id}', [ReferenceTypeController::class, 'destroy']);
});

// Bank CRUD routes
Route::prefix('banks')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [BankController::class, 'index']); // Get all banks
    Route::post('/', [BankController::class, 'store']); // Create a new bank
    Route::put('{id}', [BankController::class, 'update']); // Update an existing bank
    Route::delete('{id}', [BankController::class, 'destroy']); // Delete a bank
});
Route::prefix('transaction-headers')->middleware('auth:sanctum')->group(function () {

    Route::get('/', [TransactionHeaderController::class, 'index']);

    Route::post('/', [TransactionHeaderController::class, 'store']);

    Route::get('{id}', [TransactionHeaderController::class, 'show']);

    Route::put('{id}', [TransactionHeaderController::class, 'update']);
    Route::patch('{id}/process', [TransactionHeaderController::class, 'updateStatusAndApprover']);
    Route::delete('{id}', [TransactionHeaderController::class, 'destroy']);
});
Route::prefix('transactions')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [TransactionController::class, 'index']);
    Route::get('/by-header/{headerId}', [TransactionController::class, 'byHeader']);
    Route::post('/', [TransactionController::class, 'store']);
    Route::get('{id}', [TransactionController::class, 'show']);
    Route::put('{id}', [TransactionController::class, 'update']);
    Route::delete('{id}', [TransactionController::class, 'destroy']);
});
