<?php

namespace App\Http\Controllers\Books;

use App\Http\Controllers\Controller;
use App\Models\BookStock;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function index(Request $request)
    {
        $query = BookStock::with('book');

        if ($request->filled('book_id')) {
            $query->where('book_id', $request->book_id);
        }

        $stocks = $query->latest()->paginate(24);

        return view('books.barcode.index', compact('stocks'));
    }
}
