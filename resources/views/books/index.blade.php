@extends('layouts.app')

@section('title', 'Katalog Buku')
@section('page_title', 'Katalog Buku Perpustakaan')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Manajemen Katalog Buku</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola judul buku, klasifikasi Dewey Decimal, dan gambar sampul.</p>
        </div>
        @hasanyrole('admin|petugas_buku')
        <a href="{{ route('books.books.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors">
            <i class="fa-solid fa-plus text-xs"></i>
            Tambah Buku Baru
        </a>
        @endhasanyrole
    </div>

    {{-- Filter Card --}}
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/80">
        <form method="GET" action="{{ route('books.books.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari judul, pengarang, ISBN, atau subyek..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>
            <div class="flex gap-2">
                <select name="category" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">-- Semua Kategori --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-900 transition-colors">
                    Cari
                </button>
            </div>
        </form>
    </div>

    {{-- Books Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse ($books as $book)
        <div class="group overflow-hidden rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/80 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div class="space-y-3">
                {{-- Cover Image & Badges --}}
                <div class="relative h-48 w-full overflow-hidden rounded-xl bg-slate-100 flex items-center justify-center">
                    @if ($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="flex flex-col items-center gap-2 text-slate-400">
                            <i class="fa-solid fa-book text-4xl"></i>
                            <span class="text-[10px]">Tanpa Sampul</span>
                        </div>
                    @endif
                    <span class="absolute top-2 right-2 rounded-full bg-slate-900/80 backdrop-blur-md px-2.5 py-0.5 text-[10px] font-semibold text-white">
                        DDC {{ $book->dewey_decimal ?? '-' }}
                    </span>
                </div>

                {{-- Book Info --}}
                <div>
                    <span class="inline-block rounded-md bg-indigo-50 px-2 py-0.5 text-[10px] font-semibold text-indigo-700 mb-1">
                        {{ $book->category ?? 'Umum' }}
                    </span>
                    <h3 class="text-sm font-bold text-slate-800 line-clamp-1 group-hover:text-indigo-600 transition-colors">
                        <a href="{{ route('books.books.show', $book) }}">{{ $book->title }}</a>
                    </h3>
                    <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">Oleh: {{ $book->author }}</p>
                </div>
            </div>

            {{-- Footer Info & Action --}}
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold text-slate-700">
                        Stok: <span class="{{ $book->available_stocks_count > 0 ? 'text-green-600 font-bold' : 'text-red-500 font-bold' }}">{{ $book->available_stocks_count }}</span> / {{ $book->stocks_count }}
                    </p>
                    <p class="text-[10px] text-slate-400 font-mono">ISBN: {{ $book->isbn ?? '-' }}</p>
                </div>

                <div class="flex items-center gap-1">
                    <a href="{{ route('books.books.show', $book) }}" title="Lihat Detail" class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-indigo-600 transition-colors">
                        <i class="fa-solid fa-eye text-xs"></i>
                    </a>
                    @hasanyrole('admin|petugas_buku')
                    <a href="{{ route('books.books.edit', $book) }}" title="Edit" class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-indigo-600 transition-colors">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </a>
                    @endhasanyrole
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full rounded-2xl bg-white p-12 text-center shadow-sm">
            <i class="fa-solid fa-book-open text-4xl text-slate-300 mb-3"></i>
            <p class="text-sm font-semibold text-slate-600">Buku tidak ditemukan</p>
            <p class="text-xs text-slate-400 mt-1">Coba kata kunci atau kategori pencarian lain.</p>
        </div>
        @endforelse
    </div>

    @if ($books->hasPages())
    <div class="pt-4">
        {{ $books->links() }}
    </div>
    @endif

</div>
@endsection
