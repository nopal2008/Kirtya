<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;

class QuickSearchController extends Controller
{
    /**
     * Perform quick spotlight search across books, users, and transactions.
     */
    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));

        if (strlen($query) < 2) {
            return response()->json([
                'books' => [],
                'users' => [],
                'transactions' => []
            ]);
        }

        $user = auth()->user();

        // 1. Search Books (accessible by all)
        $books = Book::where('title', 'LIKE', "%{$query}%")
            ->orWhere('isbn', 'LIKE', "%{$query}%")
            ->orWhere('author', 'LIKE', "%{$query}%")
            ->select('id', 'title', 'author', 'isbn', 'publisher')
            ->limit(5)
            ->get();

        $users = [];
        $transactions = [];

        // 2. Search Users & Transactions (admin/petugas only)
        if ($user && $user->hasAnyRole(['admin', 'petugas_admin', 'petugas_buku'])) {
            $users = User::where('name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->orWhere('nisn_nip', 'LIKE', "%{$query}%")
                ->select('id', 'name', 'email', 'nisn_nip')
                ->limit(5)
                ->get();

            $transactions = Transaction::with(['user:id,name', 'bookStock.book:id,title'])
                ->where('transaction_code', 'LIKE', "%{$query}%")
                ->select('id', 'transaction_code', 'user_id', 'book_stock_id', 'status', 'borrow_date')
                ->limit(5)
                ->get();
        }

        return response()->json([
            'books' => $books,
            'users' => $users,
            'transactions' => $transactions,
        ]);
    }
}
