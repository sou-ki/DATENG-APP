<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\InternalDashboardController;
use App\Http\Controllers\SecurityDashboardController;
use App\Http\Controllers\VisitRequestController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CheckOutController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'role:internal'])->prefix('internal')->name('internal.')->group(function () {
    Route::get('/dashboard', [InternalDashboardController::class, 'index']);
    Route::get('/visit-requests/create', [VisitRequestController::class, 'create']);
    Route::post('/visit-requests', [VisitRequestController::class, 'store']);
    Route::get('/visit-requests/{status?}', [VisitRequestController::class, 'index']);
});

Route::middleware(['auth', 'role:security'])->prefix('security')->name('security.')->group(function () {
    Route::get('/dashboard', [SecurityDashboardController::class, 'index']);
    Route::get('/checkin', [CheckInController::class, 'index']);
    Route::post('/checkin/{visitRequest}', [CheckInController::class, 'process']);
    Route::get('/checkout', [CheckOutController::class, 'index']);
    Route::post('/checkout/{visitRequest}', [CheckOutController::class, 'process']);
});

// Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
//     Route::get('/dashboard', [AdminDashboardController::class, 'index']);
//     // User management, reports, etc.
// });