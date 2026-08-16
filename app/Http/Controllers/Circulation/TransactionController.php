<?php

namespace App\Http\Controllers\Circulation;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions.
     */
    public function index(Request $request)
    {
        $transactions = Transaction::with(['user:id,name,member_id', 'bookStock.book:id,title'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('circulation.transactions.index', compact('transactions'));
    }

    /**
     * Display the specified transaction (optional placeholder).
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'bookStock.book', 'processor']);
        return view('circulation.transactions.show', compact('transaction'));
    }
}
