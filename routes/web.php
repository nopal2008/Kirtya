<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
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
use App\Http\Controllers\Circulation\TransactionController;
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
        DB::connection()->getPdo();

        // Check Redis connection (if configured)
        if (config('cache.default') === 'redis') {
            Cache::get('health-check');
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
        // Barcode index for printing
        Route::get('barcode', [BarcodeController::class, 'index'])->name('barcode.index');

        Route::resource('books', BookController::class);
        Route::resource('stocks', BookStockController::class);
        Route::get('barcode/generate/{book}', [BarcodeController::class, 'generate'])->name('barcode.generate');
        Route::get('stock-opname', [StockOpnameController::class, 'index'])->name('stock-opname.index');
        Route::post('stock-opname/check', [StockOpnameController::class, 'check'])->name('stock-opname.check');
        Route::post('stock-opname/adjust', [StockOpnameController::class, 'adjust'])->name('stock-opname.adjust');
    });

    // Circulation Module
    Route::prefix('circulation')->name('circulation.')->group(function () {
        Route::resource('transactions', TransactionController::class);
        Route::resource('borrow', BorrowController::class);
        Route::post('borrow/{transaction}/approve', [BorrowController::class, 'approve'])->name('borrow.approve');
        Route::post('borrow/{transaction}/reject', [BorrowController::class, 'reject'])->name('borrow.reject');

        Route::resource('return', ReturnController::class);
        Route::post('return/{transaction}/process', [ReturnController::class, 'processReturn'])->name('return.process');

        // Fines (payments & management)
        Route::resource('fines', FinePaymentController::class);
        Route::post('fines/{fine}/process', [FinePaymentController::class, 'processPayment'])->name('fines.process');

        // Backwards-compatible legacy route (kept for existing links)
        Route::resource('fine-payment', FinePaymentController::class);
        Route::post('fine-payment/{fine}/pay', [FinePaymentController::class, 'pay'])->name('fine-payment.pay');

        // Visitor routes: support both `visitor-log` and `visitors` naming used across views
        Route::resource('visitors', VisitorLogController::class);
        Route::resource('visitor-log', VisitorLogController::class);
    });

    // Members Module
    Route::prefix('members')->name('members.')->group(function () {
        Route::resource('members', MemberController::class);
        Route::get('members/{member}/card', [MemberController::class, 'printCard'])->name('members.card');
    });

    // Public member-facing routes (OPAC, member dashboard pages)
    Route::get('opac', [MemberController::class, 'opac'])->name('opac.index');
    Route::get('opac/scan', [MemberController::class, 'opacScan'])->name('opac.scan');
    Route::get('opac/{book}', [MemberController::class, 'opacShow'])->name('opac.show');

    Route::get('member/card', [MemberController::class, 'card'])->name('member.card');
    Route::get('member/history', [MemberController::class, 'history'])->name('member.history');
    Route::get('member/bookings', [MemberController::class, 'bookings'])->name('member.bookings');
    Route::post('member/bookings/{book}', [MemberController::class, 'storeBooking'])->name('member.bookings.store');
    Route::get('member/fines', [MemberController::class, 'fines'])->name('member.fines');

    // Admin Module (Admin & Super Admin only)
    Route::middleware(['role:admin|super-admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('fine-settings', FineSettingController::class);
        // Named routes for admin fines settings page (views expect admin.fines.settings)
        Route::get('fines/settings', [FineSettingController::class, 'edit'])->name('fines.settings');
        Route::put('fines/settings', [FineSettingController::class, 'update'])->name('fines.settings.update');
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('metrics', [MetricsController::class, 'index'])->name('metrics.index');
    });
});
