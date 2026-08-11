@extends('layouts.app')

@section('title', 'Detail Buku - ' . $book->title)
@section('page_title', 'Detail Buku OPAC')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start">
            <div class="w-full lg:w-1/3 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/80">
                <div class="relative h-80 w-full overflow-hidden rounded-3xl bg-slate-100 flex items-center justify-center">
                    @if ($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}"
                            class="h-full w-full object-cover" />
                    @else
                        <div class="flex flex-col items-center gap-3 text-slate-400">
                            <i class="fa-solid fa-book text-6xl"></i>
                            <span class="text-sm">Tidak ada sampul buku</span>
                        </div>
                    @endif
                </div>

                <div class="mt-5 space-y-3">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-slate-400">Judul</p>
                        <h1 class="text-xl font-bold text-slate-900">{{ $book->title }}</h1>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm text-slate-600">
                        <div class="rounded-2xl bg-slate-50 p-3">
                            <p class="text-[10px] uppercase tracking-wider text-slate-400">Pengarang</p>
                            <p>{{ $book->author ?? '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-3">
                            <p class="text-[10px] uppercase tracking-wider text-slate-400">ISBN</p>
                            <p>{{ $book->isbn ?? '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-3">
                            <p class="text-[10px] uppercase tracking-wider text-slate-400">Penerbit</p>
                            <p>{{ $book->publisher ?? '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-3">
                            <p class="text-[10px] uppercase tracking-wider text-slate-400">Kategori</p>
                            <p>{{ $book->category ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-2/3 space-y-6">
                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/80">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-slate-400">Ringkasan Buku</p>
                            <h2 class="text-lg font-semibold text-slate-900">Informasi lengkap buku</h2>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('opac.index') }}"
                                class="rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 transition-colors">
                                Kembali ke Katalog
                            </a>
                            <a href="{{ route('opac.scan') }}"
                                class="rounded-full bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700 transition-colors">
                                <i class="fa-solid fa-barcode mr-2"></i> Scan Barcode Lain
                            </a>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4 text-sm text-slate-600">
                        <p><span class="font-semibold text-slate-900">Deskripsi:</span>
                            {{ $book->description ?? 'Belum ada deskripsi untuk buku ini.' }}</p>
                        <p><span class="font-semibold text-slate-900">Rak / Lokasi:</span>
                            {{ $book->rack_location ?? '-' }}
                        </p>
                        <p><span class="font-semibold text-slate-900">Tahun Terbit:</span>
                            {{ $book->publication_year ?? '-' }}</p>
                        <p><span class="font-semibold text-slate-900">Jumlah Halaman:</span> {{ $book->page_count ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/80">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-slate-400">Stok Eksemplar</p>
                            <h2 class="text-lg font-semibold text-slate-900">Daftar eksemplar tersedia</h2>
                        </div>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                            {{ $book->availableStocks->count() }} tersedia
                        </span>
                    </div>

                    <div class="mt-5 grid gap-3">
                        @forelse ($book->availableStocks as $stock)
                            <div class="rounded-2xl border border-slate-200 p-4 text-sm text-slate-700">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-semibold">Kode Eksemplar: {{ $stock->code ?? 'N/A' }}</p>
                                        <p class="text-xs text-slate-500">Status:
                                            {{ ucfirst($stock->status ?? 'tidak diketahui') }}</p>
                                    </div>
                                    <span
                                        class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600">
                                        {{ $stock->status === 'available' ? 'Tersedia' : 'Tidak tersedia' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-slate-200 p-4 text-sm text-slate-500">
                                Tidak ada eksemplar tersedia untuk buku ini saat ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
