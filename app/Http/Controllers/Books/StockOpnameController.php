<?php

namespace App\Http\Controllers\Books;

use App\Http\Controllers\Controller;
use App\Models\BookStock;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    public function index()
    {
        $stats = [
            'total_physical' => BookStock::count(),
            'good_condition' => BookStock::where('condition', 'good')->count(),
            'damaged'        => BookStock::where('condition', 'damaged')->count(),
            'lost'           => BookStock::where('condition', 'lost')->count(),
        ];

        $stocks = BookStock::with('book')->latest()->paginate(15);

        return view('books.stock-opname.index', compact('stats', 'stocks'));
    }
}
