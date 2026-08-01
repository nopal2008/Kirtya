@extends('layouts.app')

@section('title', 'Tambah Buku Baru')
@section('page_title', 'Tambah Katalog Buku Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Formulir Tambah Buku</h2>
            <p class="text-xs text-slate-500 mt-0.5">Input detail bibliografis dan jumlah eksemplar fisik awal.</p>
        </div>
        <a href="{{ route('books.books.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/80">
        <form method="POST" action="{{ route('books.books.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Judul Buku --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Judul Buku *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
                    @error('title') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Pengarang --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Pengarang / Penulis *</label>
                    <input type="text" name="author" value="{{ old('author') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>

                {{-- ISBN --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Nomor ISBN</label>
                    <input type="text" name="isbn" value="{{ old('isbn') }}" placeholder="978-602-xxxx-xx-x"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-mono">
                </div>

                {{-- Penerbit --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Penerbit</label>
                    <input type="text" name="publisher" value="{{ old('publisher') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>

                {{-- Tahun Terbit --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Tahun Terbit</label>
                    <input type="number" name="publication_year" value="{{ old('publication_year', date('Y')) }}" min="1800" max="{{ date('Y') + 1 }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>

                {{-- Kategori / Genre --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Kategori / Genre</label>
                    <input type="text" name="category" value="{{ old('category') }}" placeholder="Fiksi, Sains, Sejarah, Pemrograman..."
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>

                {{-- Nomor Dewey Decimal (DDC) --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Klasifikasi Dewey Decimal (DDC)</label>
                    <input type="text" name="dewey_decimal" value="{{ old('dewey_decimal') }}" placeholder="005.13 / 813 / 500"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-mono">
                </div>

                {{-- Lokasi Rak --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Lokasi Rak Penyimpanan</label>
                    <input type="text" name="rack_location" value="{{ old('rack_location') }}" placeholder="Rak A-01, Lantai 2"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>

                {{-- Jumlah Stok Awal --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Jumlah Eksemplar Stok Awal *</label>
                    <input type="number" name="initial_stock" value="{{ old('initial_stock', 1) }}" min="1" max="50" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-bold">
                    <p class="text-[10px] text-slate-400 mt-1">Barcode &amp; NIB akan diproduksi otomatis sejumlah eksemplar ini.</p>
                </div>

                {{-- Sampul Gambar --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Gambar Sampul Buku</label>
                    <input type="file" name="cover_image" accept="image/*"
                           class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                {{-- Deskripsi --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Deskripsi / Sinopsis Buku</label>
                    <textarea name="description" rows="4" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">{{ old('description') }}</textarea>
                </div>

            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <a href="{{ route('books.books.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700">
                    Simpan Buku &amp; Eksemplar
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
