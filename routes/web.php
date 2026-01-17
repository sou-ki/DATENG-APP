<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\InternalDashboardController;
use App\Http\Controllers\SecurityDashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Role-based routes
    Route::middleware(['role:internal'])->prefix('internal')->name('internal.')->group(function () {
        Route::get('/dashboard', [InternalDashboardController::class, 'index'])
            ->name('dashboard');

        // Placeholder routes untuk nanti
        Route::get('/visit-requests/create', function () {
            return view('internal.visit-requests.create');
        })->name('visit-requests.create');

        Route::get('/visit-requests', function () {
            return view('internal.visit-requests.index');
        })->name('visit-requests.index');

        Route::get('/visitors', function () {
            return view('internal.visitors.index');
        })->name('visitors.index');
    });

    Route::middleware(['role:security'])->prefix('security')->name('security.')->group(function () {
        Route::get('/dashboard', [SecurityDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/checkin', function () {
            return view('security.checkin');
        })->name('checkin');

        Route::post('/checkin/{visitRequest}', function () {
            // Will be implemented later
        })->name('checkin.process');

        Route::get('/checkout', function () {
            return view('security.checkout');
        })->name('checkout');

        Route::post('/checkout/{visitRequest}', function () {
            // Will be implemented later
        })->name('checkout.process');

        Route::get('/active-visits', function () {
            return view('security.active-visits');
        })->name('active-visits');

        Route::get('/badges', function () {
            return view('security.badges');
        })->name('badges');

        Route::get('/reports', function () {
            return view('security.reports');
        })->name('reports');
    });

    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/users', function () {
            return view('admin.users.index');
        })->name('users.index');

        Route::get('/users/create', function () {
            return view('admin.users.create');
        })->name('users.create');

        Route::get('/divisions', function () {
            return view('admin.divisions.index');
        })->name('divisions.index');

        Route::get('/badges', function () {
            return view('admin.badges.index');
        })->name('badges.index');

        Route::get('/reports/visits', function () {
            return view('admin.reports.visits');
        })->name('reports.visits');

        Route::get('/audit/logs', function () {
            return view('admin.audit.logs');
        })->name('audit.logs');

        Route::get('/system/status', function () {
            return view('admin.system.status');
        })->name('system.status');
    });

    // Fallback dashboard untuk role yang tidak match
    Route::get('/dashboard', function () {
        $user = Auth::user();
        
        return match($user->role) {
            'internal' => redirect()->route('internal.dashboard'),
            'security' => redirect()->route('security.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('login')
        };
    })->name('dashboard');
});