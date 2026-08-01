<?php

namespace App\Http\Controllers\Circulation;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use App\Models\FineSetting;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'bookStock.book'])->active();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")->orWhere('member_id', 'like', "%{$search}%"))
                    ->orWhereHas('bookStock', fn($s) => $s->where('barcode', 'like', "%{$search}%"));
            });
        }

        $activeLoans = $query->latest()->paginate(15)->withQueryString();

        return view('circulation.return.index', compact('activeLoans'));
    }

    public function processReturn(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'return_date'    => ['required', 'date'],
            'book_condition' => ['required', Rule::in(['good', 'damaged', 'lost'])],
            'notes'          => ['nullable', 'string'],
        ]);

        if ($transaction->status === 'returned') {
            return back()->with('error', 'Transaksi peminjaman ini sudah dikembalikan.');
        }

        $returnDate = \Carbon\Carbon::parse($validated['return_date']);
        $dueDate    = $transaction->due_date;
        $setting    = FineSetting::getActive();
        $dailyRate  = $setting?->daily_rate ?? 1000;

        // Hitung keterlambatan
        $overdueDays = $dueDate->diffInDays($returnDate, false);
        $overdueDays = $overdueDays > 0 ? (int) $overdueDays : 0;

        $fineAmount = 0;
        $fineType   = null;

        // 1. Denda keterlambatan
        if ($overdueDays > 0) {
            $fineAmount += ($overdueDays * $dailyRate);
            $fineType    = 'overdue';
        }

        // 2. Denda Kerusakan / Kehilangan jika ada
        if ($validated['book_condition'] === 'damaged') {
            $fineAmount += ($setting?->damage_fee ?? 50000);
            $fineType    = $fineType ? $fineType : 'damage';
        } elseif ($validated['book_condition'] === 'lost') {
            $bookPrice   = $transaction->bookStock->acquisition_price ?? 100000;
            $multiplier  = $setting?->lost_fee_multiplier ?? 2.00;
            $fineAmount += ($bookPrice * $multiplier);
            $fineType    = 'lost';
        }

        // Simpan Record Denda jika nominal > 0
        if ($fineAmount > 0) {
            Fine::create([
                'transaction_id' => $transaction->id,
                'user_id'        => $transaction->user_id,
                'type'           => $fineType ?? 'overdue',
                'overdue_days'   => $overdueDays,
                'daily_rate'     => $dailyRate,
                'amount'         => $fineAmount,
                'payment_status' => 'unpaid',
            ]);
        }

        // Update status transaksi
        $transaction->update([
            'return_date'  => $returnDate->toDateString(),
            'status'       => $validated['book_condition'] === 'lost' ? 'lost' : 'returned',
            'processed_by' => auth()->id(),
            'notes'        => $validated['notes'] ?? null,
        ]);

        // Update status eksemplar stok
        $stockStatus    = $validated['book_condition'] === 'lost' ? 'maintenance' : 'available';
        $stockCondition = $validated['book_condition'];

        $transaction->bookStock->update([
            'status'    => $stockStatus,
            'condition' => $stockCondition,
        ]);

        $msg = "Pengembalian buku berhasil diproses.";
        if ($fineAmount > 0) {
            $formattedFine = number_format($fineAmount, 0, ',', '.');
            $msg .= " Anggota dikenakan denda sebesar Rp {$formattedFine}.";
        }

        return redirect()->route('circulation.return.index')->with('success', $msg);
    }
}
