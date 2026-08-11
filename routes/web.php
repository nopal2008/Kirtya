<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuickSearchController;
use App\Http\Controllers\MetricsController;

// Books
use App\Http\Controllers\Books\BookController;
use App\Http\Controllers\Books\BookStockController;
use App\Http\Controllers\Books\BarcodeController;
use App\Http\Controllers\Books\StockOpnameController;

// Circulation
use App\Http\Controllers\Circulation\BorrowController;
use App\Http\Controllers\Circulation\ReturnController;
use App\Http\Controllers\Circulation\FinePaymentController;
use App\Http\Controllers\Circulation\VisitorLogController;

// Members
use App\Http\Controllers\Member\MemberController;

// Admin
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\FineSettingController;
use App\Http\Controllers\Admin\AuditLogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Health check endpoint for Docker
Route::get('/health', function () {
    try {
        // Check database connection
        \DB::connection()->getPdo();

        // Check Redis connection (if configured)
        if (config('cache.default') === 'redis') {
            \Cache::get('health-check');
        }

        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'app' => config('app.name'),
            'environment' => app()->environment(),
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'error' => app()->environment('production') ? 'Service unavailable' : $e->getMessage(),
            'timestamp' => now()->toIso8601String(),
        ], 503);
    }
})->name('health');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Quick Search
    Route::get('/quick-search', [QuickSearchController::class, 'index'])->name('quick-search');

    // Books Module
    Route::prefix('books')->name('books.')->group(function () {
        Route::resource('books', BookController::class);
        Route::resource('stocks', BookStockController::class);
        Route::get('barcode/generate/{book}', [BarcodeController::class, 'generate'])->name('barcode.generate');
        Route::get('stock-opname', [StockOpnameController::class, 'index'])->name('stock-opname.index');
        Route::post('stock-opname/check', [StockOpnameController::class, 'check'])->name('stock-opname.check');
        Route::post('stock-opname/adjust', [StockOpnameController::class, 'adjust'])->name('stock-opname.adjust');
    });

    // Circulation Module
    Route::prefix('circulation')->name('circulation.')->group(function () {
        Route::resource('borrow', BorrowController::class);
        Route::post('borrow/{transaction}/approve', [BorrowController::class, 'approve'])->name('borrow.approve');
        Route::post('borrow/{transaction}/reject', [BorrowController::class, 'reject'])->name('borrow.reject');

        Route::resource('return', ReturnController::class);
        Route::post('return/{transaction}/process', [ReturnController::class, 'processReturn'])->name('return.process');

        Route::resource('fine-payment', FinePaymentController::class);
        Route::post('fine-payment/{fine}/pay', [FinePaymentController::class, 'pay'])->name('fine-payment.pay');

        Route::resource('visitor-log', VisitorLogController::class);
    });

    // Members Module
    Route::prefix('members')->name('members.')->group(function () {
        Route::resource('members', MemberController::class);
        Route::get('members/{member}/card', [MemberController::class, 'printCard'])->name('members.card');
    });

    // Admin Module (Admin & Super Admin only)
    Route::middleware(['role:admin|super-admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('fine-settings', FineSettingController::class);
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('metrics', [MetricsController::class, 'index'])->name('metrics.index');
    });
});
