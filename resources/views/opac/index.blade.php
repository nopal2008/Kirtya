@extends('layouts.app')

@section('title', 'OPAC Katalog Buku')
@section('page_title', 'OPAC - Katalog Buku Perpustakaan Online')

@section('content')
<div class="space-y-6">

    {{-- Hero Search Banner --}}
    <div class="rounded-3xl bg-gradient-to-r from-indigo-900 via-indigo-800 to-blue-900 p-8 text-white shadow-xl">
        <div class="max-w-2xl mx-auto text-center space-y-3">
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Katalog Online Perpustakaan</h1>
            <p class="text-xs sm:text-sm text-indigo-200">Cari buku favoritmu, cek lokasi rak, dan lakukan booking secara langsung.</p>

            <form method="GET" action="{{ route('opac.index') }}" class="pt-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Ketik judul buku, pengarang, atau nomor ISBN..."
                           class="w-full rounded-2xl border-0 bg-white/10 backdrop-blur-md px-5 py-3.5 pr-28 text-sm text-white placeholder-indigo-200 focus:bg-white focus:text-slate-900 focus:outline-none focus:ring-4 focus:ring-indigo-400/30 transition-all">
                    <button type="submit" class="absolute right-2 top-1.5 bottom-1.5 rounded-xl bg-indigo-500 px-5 text-xs font-semibold text-white hover:bg-indigo-600 transition-colors">
                        Cari Buku
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Filter Categories --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs scrollbar-hide">
        <a href="{{ route('opac.index') }}" class="rounded-xl px-4 py-2 font-semibold flex-shrink-0 transition-colors {{ !request('category') ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50' }}">
            Semua Kategori
        </a>
        @foreach ($categories as $cat)
        <a href="{{ route('opac.index', ['category' => $cat]) }}" class="rounded-xl px-4 py-2 font-semibold flex-shrink-0 transition-colors {{ request('category') == $cat ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50' }}">
            {{ $cat }}
        </a>
        @endforeach
    </div>

    {{-- Books Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($books as $book)
        <div class="group overflow-hidden rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/80 hover:shadow-lg transition-all duration-200 flex flex-col justify-between">
            <div class="space-y-3">
                <div class="relative h-56 w-full overflow-hidden rounded-xl bg-slate-100 flex items-center justify-center">
                    @if ($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="flex flex-col items-center gap-2 text-slate-400">
                            <i class="fa-solid fa-book text-5xl"></i>
                            <span class="text-xs">Tanpa Sampul</span>
                        </div>
                    @endif
                    <span class="absolute top-2 right-2 rounded-full bg-slate-900/80 backdrop-blur-md px-2.5 py-0.5 text-[10px] font-semibold text-white">
                        Rak: {{ $book->rack_location ?? '-' }}
                    </span>
                </div>

                <div>
                    <span class="inline-block rounded-md bg-indigo-50 px-2 py-0.5 text-[10px] font-semibold text-indigo-700 mb-1">
                        {{ $book->category ?? 'Umum' }}
                    </span>
                    <h3 class="text-sm font-bold text-slate-800 line-clamp-1">
                        {{ $book->title }}
                    </h3>
                    <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">Penulis: {{ $book->author }}</p>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase font-semibold text-slate-400">Ketersediaan</span>
                    <p class="text-xs font-extrabold {{ $book->available_stocks_count > 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $book->available_stocks_count > 0 ? $book->available_stocks_count . ' Eksemplar' : 'Stok Habis' }}
                    </p>
                </div>

                @hasanyrole('siswa')
                @if ($book->available_stocks_count > 0)
                <form method="POST" action="{{ route('member.bookings.store', $book) }}">
                    @csrf
                    <button type="submit" class="rounded-xl bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700 shadow-sm transition-colors">
                        <i class="fa-solid fa-bookmark mr-1"></i> Booking
                    </button>
                </form>
                @endif
                @endhasanyrole
            </div>
        </div>
        @empty
        <div class="col-span-full rounded-2xl bg-white p-12 text-center shadow-sm">
            <i class="fa-solid fa-magnifying-glass text-4xl text-slate-300 mb-3"></i>
            <p class="text-sm font-semibold text-slate-600">Buku tidak ditemukan di katalog</p>
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
