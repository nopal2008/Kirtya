<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookStock;
use App\Models\Fine;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama.
     * Data statistik disesuaikan berdasarkan peran pengguna yang sedang login.
     */
    public function index()
    {
        $user = auth()->user();

        // ------------------------------------------------------------------
        // Statistik Utama (Admin & Petugas)
        // ------------------------------------------------------------------
        $stats = [
            'total_books'        => Book::count(),
            'total_stocks'       => BookStock::count(),
            'available_stocks'   => BookStock::where('status', 'available')->count(),
            'total_active_members' => User::where('status', 'active')
                                         ->whereHas('roles', fn ($q) => $q->where('name', 'siswa'))
                                         ->count(),
            'total_borrowed'     => Transaction::whereIn('status', ['borrowed', 'overdue'])->count(),
            'total_overdue'      => Transaction::where('status', 'overdue')
                                               ->orWhere(function ($q) {
                                                   $q->where('status', 'borrowed')
                                                     ->where('due_date', '<', now()->toDateString());
                                               })
                                               ->count(),
            'total_unpaid_fines' => Fine::where('payment_status', 'unpaid')->sum('amount'),
            'total_fines_today'  => Fine::where('payment_status', 'paid')
                                        ->whereDate('paid_at', today())
                                        ->sum('amount'),
        ];

        // ------------------------------------------------------------------
        // Transaksi Terbaru (10 data terakhir)
        // ------------------------------------------------------------------
        $recentTransactions = Transaction::with([
            'user:id,name,member_id',
            'bookStock.book:id,title,author',
            'processor:id,name',
        ])
        ->latest()
        ->take(10)
        ->get();

        // ------------------------------------------------------------------
        // Denda Belum Dibayar (5 terbesar)
        // ------------------------------------------------------------------
        $pendingFines = Fine::with(['user:id,name,member_id', 'transaction.bookStock.book:id,title'])
                           ->where('payment_status', 'unpaid')
                           ->orderByDesc('amount')
                           ->take(5)
                           ->get();

        // ------------------------------------------------------------------
        // Buku Paling Banyak Dipinjam (Top 5)
        // ------------------------------------------------------------------
        $popularBooks = Book::withCount('stocks as total_stocks')
                            ->whereHas('stocks', fn ($q) => $q->whereHas('transactions'))
                            ->take(5)
                            ->get();

        return view('dashboard', compact(
            'stats',
            'recentTransactions',
            'pendingFines',
            'popularBooks',
        ));
    }
}
