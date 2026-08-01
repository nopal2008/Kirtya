@extends('layouts.app')

@section('title', 'Detail Buku')
@section('page_title', $book->title)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('books.books.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Katalog
        </a>
        @hasanyrole('admin|petugas_buku')
        <div class="flex gap-2">
            <a href="{{ route('books.books.edit', $book) }}" class="rounded-xl bg-slate-800 px-3.5 py-2 text-xs font-semibold text-white hover:bg-slate-900">
                <i class="fa-solid fa-pen mr-1"></i> Edit Buku
            </a>
        </div>
        @endhasanyrole
    </div>

    {{-- Main Book Detail Card --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/80">
        <div class="flex flex-col md:flex-row gap-6">

            {{-- Cover Image --}}
            <div class="w-full md:w-56 h-72 rounded-xl bg-slate-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                @if ($book->cover_image)
                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="h-full w-full object-cover">
                @else
                    <div class="text-center text-slate-400">
                        <i class="fa-solid fa-book text-5xl mb-2"></i>
                        <p class="text-xs">Tanpa Sampul</p>
                    </div>
                @endif
            </div>

            {{-- Info Details --}}
            <div class="flex-1 space-y-4">
                <div>
                    <span class="inline-block rounded-md bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                        {{ $book->category ?? 'Umum' }}
                    </span>
                    <h1 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $book->title }}</h1>
                    <p class="text-sm font-medium text-slate-500">Penulis / Pengarang: <span class="text-slate-800">{{ $book->author }}</span></p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                    <div class="rounded-xl bg-slate-50 p-3">
                        <span class="text-[10px] uppercase font-semibold text-slate-400">ISBN</span>
                        <p class="font-bold text-slate-800 font-mono mt-0.5">{{ $book->isbn ?? '-' }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3">
                        <span class="text-[10px] uppercase font-semibold text-slate-400">Dewey Decimal</span>
                        <p class="font-bold text-indigo-700 font-mono mt-0.5">{{ $book->dewey_decimal ?? '-' }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3">
                        <span class="text-[10px] uppercase font-semibold text-slate-400">Lokasi Rak</span>
                        <p class="font-bold text-slate-800 mt-0.5">{{ $book->rack_location ?? '-' }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3">
                        <span class="text-[10px] uppercase font-semibold text-slate-400">Penerbit</span>
                        <p class="font-bold text-slate-800 mt-0.5">{{ $book->publisher ?? '-' }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3">
                        <span class="text-[10px] uppercase font-semibold text-slate-400">Tahun Terbit</span>
                        <p class="font-bold text-slate-800 mt-0.5">{{ $book->publication_year ?? '-' }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3">
                        <span class="text-[10px] uppercase font-semibold text-slate-400">Stok Tersedia</span>
                        <p class="font-bold text-green-600 mt-0.5">{{ $book->available_stock_count }} / {{ $book->total_stock }}</p>
                    </div>
                </div>

                @if ($book->description)
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Sinopsis / Deskripsi</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $book->description }}</p>
                </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Physical Stock Table --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/80">
        <h3 class="text-sm font-bold text-slate-800 mb-3">Daftar Eksemplar Fisik ({{ $book->stocks->count() }})</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-2.5">Kode Barcode</th>
                        <th class="px-4 py-2.5">Nomor Induk (NIB)</th>
                        <th class="px-4 py-2.5">Kondisi</th>
                        <th class="px-4 py-2.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($book->stocks as $st)
                    <tr>
                        <td class="px-4 py-2.5 font-mono font-bold text-indigo-700">{{ $st->barcode }}</td>
                        <td class="px-4 py-2.5 font-mono text-slate-600">{{ $st->accession_number }}</td>
                        <td class="px-4 py-2.5">
                            <span class="inline-block rounded-md px-2 py-0.5 text-[10px] font-semibold {{ $st->condition === 'good' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($st->condition) }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5">
                            <span class="inline-block rounded-md px-2 py-0.5 text-[10px] font-semibold {{ $st->status === 'available' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ ucfirst($st->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-slate-400">Belum ada eksemplar fisik.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
