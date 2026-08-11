<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\FineSettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Books\BarcodeController;
use App\Http\Controllers\Books\BookController;
use App\Http\Controllers\Books\BookStockController;
use App\Http\Controllers\Books\StockOpnameController;
use App\Http\Controllers\Circulation\BorrowController;
use App\Http\Controllers\Circulation\FinePaymentController;
use App\Http\Controllers\Circulation\ReturnController;
use App\Http\Controllers\Circulation\VisitorLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Member\MemberController;
use App\Http\Controllers\MetricsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuickSearchController;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Prometheus & Grafana Monitoring Endpoint
Route::get('/metrics', [MetricsController::class, 'metrics'])->name('metrics');

// Landing page
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
});

// ============================================================
// Auth Routes (With Rate Limiting against Brute Force)
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    // Rate limit: 5 attempts per 15 minutes untuk mencegah brute force
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,15')
        ->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ============================================================
// Authenticated Routes
// ============================================================
Route::middleware(['auth'])->group(function () {

    // Global Spotlight Quick Search API
    Route::get('/quick-search', [QuickSearchController::class, 'search'])->name('quick-search');

    // Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // OPAC Catalog (Semua Pengguna Terautentikasi)
    Route::get('/opac', [MemberController::class, 'opac'])->name('opac.index');
    Route::get('/opac/scan', [MemberController::class, 'opacScan'])->name('opac.scan');
    Route::get('/opac/{book}', [MemberController::class, 'opacShow'])->name('opac.show');

    // --------------------------------------------------------
    // Admin: Manajemen Pengguna & Konfigurasi
    // --------------------------------------------------------
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);

        Route::get('fines/settings', [FineSettingController::class, 'edit'])->name('fines.settings');
        Route::put('fines/settings', [FineSettingController::class, 'update'])->name('fines.settings.update');

        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    // --------------------------------------------------------
    // Sirkulasi: Petugas Admin & Admin
    // --------------------------------------------------------
    Route::middleware(['role:admin|petugas_admin'])->prefix('circulation')->name('circulation.')->group(function () {
        Route::get('borrow/create', [BorrowController::class, 'create'])->name('borrow.create');
        Route::post('borrow', [BorrowController::class, 'store'])->name('borrow.store');
        Route::post('borrow/{booking}/approve', [BorrowController::class, 'approveBooking'])->name('borrow.approve');
        Route::post('borrow/{booking}/reject', [BorrowController::class, 'rejectBooking'])->name('borrow.reject');

        Route::get('return', [ReturnController::class, 'index'])->name('return.index');
        Route::post('return/{transaction}', [ReturnController::class, 'processReturn'])->name('return.process');

        Route::get('fines', [FinePaymentController::class, 'index'])->name('fines.index');
        Route::post('fines/{fine}', [FinePaymentController::class, 'processPayment'])->name('fines.process');

        Route::get('visitors', [VisitorLogController::class, 'index'])->name('visitors.index');
        Route::post('visitors', [VisitorLogController::class, 'store'])->name('visitors.store');

        Route::get('transactions', function () {
            $transactions = Transaction::with(['user', 'bookStock.book'])->latest()->paginate(15);
            return view('circulation.transactions.index', compact('transactions'));
        })->name('transactions.index');
    });

    // --------------------------------------------------------
    // Koleksi Buku: Petugas Buku & Admin
    // --------------------------------------------------------
    Route::middleware(['role:admin|petugas_buku'])->group(function () {
        Route::resource('books', BookController::class, ['names' => 'books.books']);

        Route::get('books-stocks', [BookStockController::class, 'index'])->name('books.stocks.index');
        Route::post('books-stocks', [BookStockController::class, 'store'])->name('books.stocks.store');
        Route::patch('books-stocks/{stock}', [BookStockController::class, 'updateStatus'])->name('books.stocks.update-status');

        Route::get('books-barcode', [BarcodeController::class, 'index'])->name('books.barcode.index');
        Route::get('books-stock-opname', [StockOpnameController::class, 'index'])->name('books.stock-opname.index');
    });

    // --------------------------------------------------------
    // Layanan Anggota / Siswa
    // --------------------------------------------------------
    Route::middleware(['role:siswa'])->prefix('member')->name('member.')->group(function () {
        Route::get('card', [MemberController::class, 'card'])->name('card');
        Route::get('history', [MemberController::class, 'history'])->name('history');
        Route::get('bookings', [MemberController::class, 'bookings'])->name('bookings');
        Route::post('bookings/{book}', [MemberController::class, 'storeBooking'])->name('bookings.store');
        Route::get('fines', [MemberController::class, 'fines'])->name('fines');
    });
});
