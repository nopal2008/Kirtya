<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookStock;
use App\Models\Fine;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    /**
     * OPAC (Online Public Access Catalog)
     */
    public function opac(Request $request)
    {
        $query = Book::withCount('availableStocks');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $books      = $query->latest()->paginate(12)->withQueryString();
        $categories = Book::distinct()->whereNotNull('category')->pluck('category');

        return view('opac.index', compact('books', 'categories'));
    }

    /**
     * Scan Barcode Buku di OPAC
     */
    public function opacScan(Request $request)
    {
        $barcode = trim($request->query('barcode', ''));
        $stock = null;
        $notFound = false;

        if ($barcode !== '') {
            $stock = BookStock::with('book')->where('barcode', $barcode)->first();

            if ($stock && $stock->book) {
                return redirect()->route('opac.show', $stock->book);
            }

            $notFound = true;
        }

        return view('opac.scan', compact('barcode', 'notFound'));
    }

    /**
     * Detail Buku di OPAC
     */
    public function opacShow(Book $book)
    {
        $book->load('availableStocks');
        return view('opac.show', compact('book'));
    }

    /**
     * Kartu Digital Siswa
     */
    public function card()
    {
        $user = auth()->user();
        return view('member.card', compact('user'));
    }

    /**
     * Riwayat Peminjaman Siswa
     */
    public function history()
    {
        $user = auth()->user();
        $transactions = Transaction::with('bookStock.book')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('member.history', compact('transactions'));
    }

    /**
     * Booking Buku oleh Siswa
     */
    public function bookings()
    {
        $user = auth()->user();
        $bookings = Transaction::with('bookStock.book')
            ->where('user_id', $user->id)
            ->where('type', 'booking')
            ->latest()
            ->paginate(10);

        return view('member.bookings', compact('bookings'));
    }

    /**
     * Proses buat Booking Buku (siswa dari OPAC — menunggu persetujuan petugas)
     */
    public function storeBooking(Request $request, Book $book)
    {
        $user = auth()->user();

        // 1. BLOKIR KERAS: jika memiliki 1 denda pun, tidak bisa booking
        if ($user->has_any_fine) {
            $formattedFine = number_format($user->unpaid_fines_total, 0, ',', '.');
            return back()->with('error', "❌ Kamu tidak dapat melakukan booking karena masih memiliki tunggakan denda sebesar Rp {$formattedFine}. Harap lunasi terlebih dahulu di kasir perpustakaan.");
        }

        // 2. Cek stok tersedia
        $stock = $book->availableStocks()->first();
        if (!$stock) {
            return back()->with('error', 'Maaf, stok eksemplar buku ini sedang habis atau tidak tersedia.');
        }

        // 3. Cek apakah user sudah punya booking/peminjaman buku ini yang belum selesai
        $existing = Transaction::where('user_id', $user->id)
            ->where('book_stock_id', $stock->id)
            ->whereIn('status', ['borrowed', 'overdue', 'pending_approval'])
            ->exists();

        if ($existing) {
            return back()->with('error', 'Kamu sudah memiliki booking atau sedang meminjam buku ini.');
        }

        $code = 'BKG-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        Transaction::create([
            'transaction_code' => $code,
            'user_id'          => $user->id,
            'book_stock_id'    => $stock->id,
            'borrow_date'      => now()->toDateString(),
            'due_date'         => now()->addDays(7)->toDateString(),
            'status'           => 'pending_approval',   // Menunggu persetujuan petugas
            'type'             => 'booking',
            'booking_expiry'   => now()->addDays(2),
            'notes'            => 'Booking via OPAC Siswa — menunggu persetujuan petugas.',
        ]);

        // Reservasi stok
        $stock->update(['status' => 'reserved']);

        return redirect()->route('member.bookings')
            ->with('success', "📚 Booking buku \"{$book->title}\" berhasil dibuat (Kode: {$code}). Menunggu persetujuan petugas perpustakaan.");
    }

    /**
     * Info Denda Siswa
     */
    public function fines()
    {
        $user  = auth()->user();
        $fines = Fine::with('transaction.bookStock.book')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        $totalUnpaid = $user->unpaid_fines_total;

        return view('member.fines', compact('fines', 'totalUnpaid'));
    }
}
