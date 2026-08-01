<?php

namespace App\Http\Controllers\Circulation;

use App\Http\Controllers\Controller;
use App\Models\BookStock;
use App\Models\FineSetting;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BorrowController extends Controller
{
    public function create()
    {
        $members        = User::role('siswa')->where('status', 'active')->get(['id', 'name', 'member_id']);
        $availableBooks = BookStock::with('book')->where('status', 'available')->where('condition', 'good')->get();
        $setting        = FineSetting::getActive();

        // Daftar booking siswa yang menunggu persetujuan petugas
        $pendingBookings = Transaction::with(['user', 'bookStock.book'])
            ->pendingApproval()
            ->latest()
            ->get();

        return view('circulation.borrow.create', compact('members', 'availableBooks', 'setting', 'pendingBookings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'       => ['required', 'exists:users,id'],
            'book_stock_id' => ['required', 'exists:book_stocks,id'],
            'borrow_date'   => ['required', 'date'],
            'notes'         => ['nullable', 'string'],
        ]);

        $user    = User::findOrFail($validated['user_id']);
        $stock   = BookStock::findOrFail($validated['book_stock_id']);
        $setting = FineSetting::getActive();

        // 1. Cek status keanggotaan
        if ($user->status !== 'active') {
            return back()->with('error', "Anggota {$user->name} tidak dalam status aktif ({$user->status}).")->withInput();
        }

        // 2. BLOKIR KERAS: cek apakah ada denda minimal 1x belum dibayar
        if ($user->has_any_fine) {
            $formattedFine = number_format($user->unpaid_fines_total, 0, ',', '.');
            return back()->with('error', "❌ Anggota {$user->name} memiliki tunggakan denda (Rp {$formattedFine}). Harap selesaikan semua denda terlebih dahulu sebelum dapat meminjam.")->withInput();
        }

        // 3. Cek apakah ada buku yang keterlambatannya belum dikembalikan
        if ($user->has_overdue_books) {
            return back()->with('error', "Anggota {$user->name} memiliki buku terlambat yang belum dikembalikan.")->withInput();
        }

        // 4. Cek batas maksimal peminjaman
        $maxLimit    = $setting?->max_borrow_limit ?? 3;
        $activeLoans = $user->transactions()->whereIn('status', ['borrowed', 'overdue'])->count();

        if ($activeLoans >= $maxLimit) {
            return back()->with('error', "Anggota {$user->name} telah mencapai batas peminjaman maksimal ({$maxLimit} buku).")->withInput();
        }

        // 5. Cek ketersediaan eksemplar
        if ($stock->status !== 'available' || $stock->condition === 'lost') {
            return back()->with('error', "Eksemplar buku ini sedang tidak tersedia untuk dipinjam.")->withInput();
        }

        // Hitung tanggal jatuh tempo
        $maxDays    = $setting?->max_borrow_days ?? 7;
        $borrowDate = Carbon::parse($validated['borrow_date']);
        $dueDate    = $borrowDate->copy()->addDays($maxDays);

        // Buat Kode Transaksi TRX-YYYYMMDD-XXXX
        $code = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        Transaction::create([
            'transaction_code' => $code,
            'user_id'          => $user->id,
            'book_stock_id'    => $stock->id,
            'processed_by'     => auth()->id(),
            'borrow_date'      => $borrowDate->toDateString(),
            'due_date'         => $dueDate->toDateString(),
            'status'           => 'borrowed',
            'type'             => 'borrow',
            'notes'            => $validated['notes'],
        ]);

        // Update status stok
        $stock->update(['status' => 'borrowed']);

        return redirect()->route('circulation.transactions.index')
            ->with('success', "Peminjaman berhasil diproses. Kode Transaksi: {$code}. Jatuh Tempo: {$dueDate->format('d M Y')}.");
    }

    /**
     * Setujui booking siswa — ubah status menjadi borrowed, proses jatuh tempo.
     */
    public function approveBooking(Transaction $booking)
    {
        if ($booking->status !== 'pending_approval') {
            return back()->with('error', 'Booking ini tidak dalam status menunggu persetujuan.');
        }

        $siswa = $booking->user;

        // Cek ulang denda di saat persetujuan
        if ($siswa->has_any_fine) {
            $formattedFine = number_format($siswa->unpaid_fines_total, 0, ',', '.');
            return back()->with('error', "❌ Tidak dapat menyetujui: {$siswa->name} masih memiliki denda (Rp {$formattedFine}). Minta siswa melunasi denda terlebih dahulu.");
        }

        $setting  = FineSetting::getActive();
        $maxDays  = $setting?->max_borrow_days ?? 7;
        $today    = Carbon::today();
        $dueDate  = $today->copy()->addDays($maxDays);

        $booking->update([
            'status'       => 'borrowed',
            'borrow_date'  => $today->toDateString(),
            'due_date'     => $dueDate->toDateString(),
            'processed_by' => auth()->id(),
        ]);

        return back()->with('success', "✅ Booking {$booking->transaction_code} milik {$siswa->name} telah disetujui. Jatuh tempo: {$dueDate->format('d M Y')}.");
    }

    /**
     * Tolak booking siswa — kembalikan stok ke available.
     */
    public function rejectBooking(Request $request, Transaction $booking)
    {
        if ($booking->status !== 'pending_approval') {
            return back()->with('error', 'Booking ini tidak dalam status menunggu persetujuan.');
        }

        $siswa = $booking->user;
        $reason = $request->input('reject_reason', 'Ditolak oleh petugas.');

        $booking->update([
            'status'       => 'rejected',
            'processed_by' => auth()->id(),
            'notes'        => $reason,
        ]);

        // Kembalikan stok ke available
        if ($booking->bookStock) {
            $booking->bookStock->update(['status' => 'available']);
        }

        return back()->with('success', "Booking {$booking->transaction_code} milik {$siswa->name} telah ditolak.");
    }
}
