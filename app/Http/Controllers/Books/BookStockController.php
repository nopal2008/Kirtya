<?php

namespace App\Http\Controllers\Books;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookStock;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookStockController extends Controller
{
    public function index(Request $request)
    {
        $query = BookStock::with('book');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('barcode', 'like', "%{$search}%")
                  ->orWhere('accession_number', 'like', "%{$search}%")
                  ->orWhereHas('book', fn ($b) => $b->where('title', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        $stocks = $query->latest()->paginate(20)->withQueryString();
        $books  = Book::all(['id', 'title']);

        return view('books.stocks.index', compact('stocks', 'books'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id'            => ['required', 'exists:books,id'],
            'condition'          => ['required', Rule::in(['good', 'damaged', 'lost'])],
            'acquisition_source' => ['nullable', 'string', 'max:255'],
            'acquisition_price'  => ['nullable', 'numeric', 'min:0'],
            'notes'              => ['nullable', 'string'],
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $count = $book->stocks()->count() + 1;

        $barcode = 'BCK-' . str_pad($book->id, 5, '0', STR_PAD_LEFT) . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $nib     = 'NIB-' . date('Y') . '-' . str_pad($book->id, 4, '0', STR_PAD_LEFT) . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        BookStock::create([
            'book_id'            => $book->id,
            'barcode'            => $barcode,
            'accession_number'   => $nib,
            'condition'          => $validated['condition'],
            'status'             => $validated['condition'] === 'lost' ? 'maintenance' : 'available',
            'acquisition_date'   => now()->toDateString(),
            'acquisition_source' => $validated['acquisition_source'],
            'acquisition_price'  => $validated['acquisition_price'],
            'notes'              => $validated['notes'],
        ]);

        return redirect()->route('books.stocks.index')
            ->with('success', "Eksemplar baru untuk buku \"{$book->title}\" berhasil ditambahkan ({$barcode}).");
    }

    public function updateStatus(Request $request, BookStock $stock)
    {
        $validated = $request->validate([
            'condition' => ['required', Rule::in(['good', 'damaged', 'lost'])],
            'status'    => ['required', Rule::in(['available', 'borrowed', 'reserved', 'maintenance'])],
            'notes'     => ['nullable', 'string'],
        ]);

        $stock->update($validated);

        return back()->with('success', "Status eksemplar {$stock->barcode} berhasil diperbarui.");
    }
}
