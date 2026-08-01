<?php

namespace App\Http\Controllers\Circulation;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FinePaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Fine::with(['user', 'transaction.bookStock.book']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('member_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        $fines = $query->latest()->paginate(15)->withQueryString();

        return view('circulation.fines.index', compact('fines'));
    }

    public function processPayment(Request $request, Fine $fine)
    {
        $validated = $request->validate([
            'action'        => ['required', Rule::in(['pay', 'waive'])],
            'waived_reason' => ['required_if:action,waive', 'nullable', 'string'],
        ]);

        if ($fine->payment_status !== 'unpaid') {
            return back()->with('error', 'Denda ini sudah lunas atau telah dibebaskan sebelumnya.');
        }

        if ($validated['action'] === 'pay') {
            $fine->update([
                'payment_status' => 'paid',
                'paid_at'        => now(),
                'paid_by'        => auth()->id(),
            ]);

            $msg = "Pembayaran denda sebesar Rp " . number_format($fine->amount, 0, ',', '.') . " berhasil dicatat.";
        } else {
            $fine->update([
                'payment_status' => 'waived',
                'paid_at'        => now(),
                'paid_by'        => auth()->id(),
                'waived_reason'  => $validated['waived_reason'],
            ]);

            $msg = "Denda sebesar Rp " . number_format($fine->amount, 0, ',', '.') . " berhasil dibebaskan.";
        }

        return redirect()->route('circulation.fines.index')->with('success', $msg);
    }
}
