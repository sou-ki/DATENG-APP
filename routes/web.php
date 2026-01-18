<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\InternalDashboardController;
use App\Http\Controllers\SecurityDashboardController;
use App\Http\Controllers\Internal\VisitorController;
use App\Http\Controllers\Internal\VisitRequestController;
use App\Http\Controllers\Security\ActiveVisitController;
use App\Http\Controllers\Security\CheckInController;
use App\Http\Controllers\Security\CheckOutController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Security\BadgeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

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

    // === INTERNAL ROUTES ===
    Route::middleware(['role:internal'])->prefix('internal')->name('internal.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [InternalDashboardController::class, 'index'])
            ->name('dashboard');
        
        // Visit Requests
        Route::resource('visit-requests', VisitRequestController::class)->except(['destroy']);
        Route::post('/visit-requests/{visitRequest}/cancel', [VisitRequestController::class, 'cancel'])
            ->name('visit-requests.cancel');
        
        // Visitors (CRUD Lengkap)
        Route::resource('visitors', VisitorController::class);
        
        // API for visitor search
        Route::get('/api/visitors/search', function (\Illuminate\Http\Request $request) {
            $query = $request->get('q');
            
            return \App\Models\Visitor::where('full_name', 'like', "%{$query}%")
                ->orWhere('identity_number', 'like', "%{$query}%")
                ->limit(10)
                ->get(['id', 'full_name', 'identity_number', 'institution']);
        });
    });

   // === SECURITY ROUTES ===
    Route::middleware(['role:security'])->prefix('security')->name('security.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [SecurityDashboardController::class, 'index'])
            ->name('dashboard');
        
        // Check-in
        Route::get('/checkin', [CheckInController::class, 'index'])->name('checkin');
        Route::post('/checkin/{visitRequest}', [CheckInController::class, 'process'])->name('checkin.process');
        
        // Check-out
        Route::get('/checkout', [CheckOutController::class, 'index'])->name('checkout');
        Route::post('/checkout/{visitRequest}', [CheckOutController::class, 'process'])->name('checkout.process');
        
        // Active Visits
        Route::get('/active-visits', [ActiveVisitController::class, 'index'])->name('active-visits');
        
        // Badges Management
        Route::get('/badges', [BadgeController::class, 'index'])->name('badges');
        Route::post('/badges/{badge}/report-issue', [BadgeController::class, 'markIssue'])->name('badges.report-issue');
        Route::post('/badges/{badge}/resolve-issue', [BadgeController::class, 'markResolved'])->name('badges.resolve-issue');
        Route::get('/badges/statistics', [BadgeController::class, 'getStatistics'])->name('badges.statistics');
    });

    // === ADMIN ROUTES ===
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard utama dengan semua fitur
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // User Management
        Route::post('/users', [AdminDashboardController::class, 'createUser'])->name('users.store');
        Route::put('/users/{user}', [AdminDashboardController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminDashboardController::class, 'deleteUser'])->name('users.destroy');
        
        // Division Management
        Route::post('/divisions', [AdminDashboardController::class, 'createDivision'])->name('divisions.store');
        Route::put('/divisions/{division}', [AdminDashboardController::class, 'updateDivision'])->name('divisions.update');
        Route::delete('/divisions/{division}', [AdminDashboardController::class, 'deleteDivision'])->name('divisions.destroy');
        
        // Badge Management
        Route::post('/badges', [AdminDashboardController::class, 'createBadge'])->name('badges.store');
        Route::put('/badges/{badge}', [AdminDashboardController::class, 'updateBadge'])->name('badges.update');
        Route::delete('/badges/{badge}', [AdminDashboardController::class, 'deleteBadge'])->name('badges.destroy');
        
        // Visitor Management
        Route::put('/visitors/{visitor}', [AdminDashboardController::class, 'updateVisitor'])->name('visitors.update');
        Route::delete('/visitors/{visitor}', [AdminDashboardController::class, 'deleteVisitor'])->name('visitors.destroy');
    });

    // === COMMON ROUTES ===
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
    
    // API Routes
    Route::prefix('api')->group(function () {
        Route::get('/visitors/search', function (\Illuminate\Http\Request $request) {
            $query = $request->get('q');
            
            if (strlen($query) < 2) {
                return [];
            }
            
            return \App\Models\Visitor::where('full_name', 'like', "%{$query}%")
                ->orWhere('identity_number', 'like', "%{$query}%")
                ->orWhere('institution', 'like', "%{$query}%")
                ->limit(10)
                ->get(['id', 'full_name', 'identity_number', 'institution', 'phone_number']);
        });
    });
});