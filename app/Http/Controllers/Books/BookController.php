<?php

namespace App\Http\Controllers\Books;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::withCount(['stocks', 'availableStocks']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->search($search);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $books = $query->latest()->paginate(12)->withQueryString();
        $categories = Book::distinct()->whereNotNull('category')->pluck('category');

        return view('books.index', compact('books', 'categories'));
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn'             => ['nullable', 'string', 'max:20', 'unique:books,isbn'],
            'title'            => ['required', 'string', 'max:255'],
            'author'           => ['required', 'string', 'max:255'],
            'publisher'        => ['nullable', 'string', 'max:255'],
            'publication_year' => ['nullable', 'integer', 'min:1800', 'max:' . (date('Y') + 1)],
            'edition'          => ['nullable', 'string', 'max:50'],
            'category'         => ['nullable', 'string', 'max:100'],
            'subject'          => ['nullable', 'string', 'max:255'],
            'dewey_decimal'    => ['nullable', 'string', 'max:30'],
            'description'      => ['nullable', 'string'],
            'pages'            => ['nullable', 'integer', 'min:1'],
            'rack_location'    => ['nullable', 'string', 'max:50'],
            'initial_stock'    => ['required', 'integer', 'min:1', 'max:50'],
            'cover_image'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
        }

        $book = Book::create([
            'isbn'             => $validated['isbn'] ?? null,
            'title'            => $validated['title'],
            'author'           => $validated['author'],
            'publisher'        => $validated['publisher'] ?? null,
            'publication_year' => $validated['publication_year'] ?? null,
            'edition'          => $validated['edition'] ?? null,
            'category'         => $validated['category'] ?? null,
            'subject'          => $validated['subject'] ?? null,
            'dewey_decimal'    => $validated['dewey_decimal'] ?? null,
            'description'      => $validated['description'] ?? null,
            'pages'            => $validated['pages'] ?? null,
            'rack_location'    => $validated['rack_location'] ?? null,
            'cover_image'      => $coverPath,
            'status'           => 'available',
        ]);

        // Generasi otomatis eksemplar stok
        for ($i = 1; $i <= $validated['initial_stock']; $i++) {
            $barcode = 'BCK-' . str_pad($book->id, 5, '0', STR_PAD_LEFT) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $nib     = 'NIB-' . date('Y') . '-' . str_pad($book->id, 4, '0', STR_PAD_LEFT) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);

            BookStock::create([
                'book_id'          => $book->id,
                'barcode'          => $barcode,
                'accession_number' => $nib,
                'condition'        => 'good',
                'status'           => 'available',
                'acquisition_date' => now()->toDateString(),
            ]);
        }

        return redirect()->route('books.books.index')
            ->with('success', "Buku \"{$book->title}\" berhasil ditambahkan beserta {$validated['initial_stock']} eksemplar stok.");
    }

    public function show(Book $book)
    {
        $book->load(['stocks.transactions.user']);
        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'isbn'             => ['nullable', 'string', 'max:20', Rule::unique('books')->ignore($book->id)],
            'title'            => ['required', 'string', 'max:255'],
            'author'           => ['required', 'string', 'max:255'],
            'publisher'        => ['nullable', 'string', 'max:255'],
            'publication_year' => ['nullable', 'integer', 'min:1800', 'max:' . (date('Y') + 1)],
            'edition'          => ['nullable', 'string', 'max:50'],
            'category'         => ['nullable', 'string', 'max:100'],
            'subject'          => ['nullable', 'string', 'max:255'],
            'dewey_decimal'    => ['nullable', 'string', 'max:30'],
            'description'      => ['nullable', 'string'],
            'pages'            => ['nullable', 'integer', 'min:1'],
            'rack_location'    => ['nullable', 'string', 'max:50'],
            'status'           => ['required', Rule::in(['available', 'unavailable'])],
            'cover_image'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $book->update($validated);

        return redirect()->route('books.books.index')
            ->with('success', "Data buku \"{$book->title}\" berhasil diperbarui.");
    }

    public function destroy(Book $book)
    {
        // Cek jika ada stok yang sedang dipinjam
        if ($book->stocks()->whereIn('status', ['borrowed', 'reserved'])->exists()) {
            return back()->with('error', 'Buku tidak dapat dihapus karena terdapat eksemplar yang sedang dipinjam.');
        }

        $title = $book->title;
        $book->delete();

        return redirect()->route('books.books.index')
            ->with('success', "Buku \"{$title}\" berhasil dihapus.");
    }
}
