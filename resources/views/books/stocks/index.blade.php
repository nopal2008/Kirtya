@extends('layouts.app')

@section('title', 'Eksemplar & Stok')
@section('page_title', 'Eksemplar & Stok Fisik Buku')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Eksemplar Fisik Buku</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola barcode unik, nomor induk (NIB), dan status fisik tiap unit buku.</p>
        </div>
    </div>

    {{-- Filter & Add Stock --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Table Section --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/80">
                <form method="GET" action="{{ route('books.stocks.index') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari Barcode, NIB, atau Judul Buku..."
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <div class="flex gap-2">
                        <select name="status" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800">
                            <option value="">-- Status --</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                            <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Booking</option>
                        </select>
                        <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-xs font-semibold text-white">Filter</button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 border-b border-slate-100 text-[10px] uppercase text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Barcode / NIB</th>
                                <th class="px-4 py-3">Judul Buku</th>
                                <th class="px-4 py-3">Kondisi</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($stocks as $st)
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-4 py-3">
                                    <p class="font-mono font-bold text-indigo-700">{{ $st->barcode }}</p>
                                    <p class="font-mono text-[10px] text-slate-400">{{ $st->accession_number }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-800 line-clamp-1">{{ $st->book->title ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block rounded-md px-2 py-0.5 text-[10px] font-semibold {{ $st->condition === 'good' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($st->condition) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block rounded-md px-2 py-0.5 text-[10px] font-semibold {{ $st->status === 'available' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ ucfirst($st->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400">Tidak ada eksemplar fisik.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($stocks->hasPages())
                <div class="p-3 border-t border-slate-100">
                    {{ $stocks->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- Add Unit Stock Form --}}
        <div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/80 space-y-4">
                <h3 class="text-sm font-bold text-slate-800">Tambah Eksemplar Baru</h3>
                <form method="POST" action="{{ route('books.stocks.store') }}" class="space-y-3">
                    @csrf

                    <div>
                        <label class="block text-[11px] font-semibold uppercase text-slate-600 mb-1">Pilih Buku *</label>
                        <select name="book_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800">
                            @foreach ($books as $b)
                                <option value="{{ $b->id }}">{{ $b->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase text-slate-600 mb-1">Kondisi Fisik *</label>
                        <select name="condition" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800">
                            <option value="good">Baik</option>
                            <option value="damaged">Rusak</option>
                            <option value="lost">Hilang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase text-slate-600 mb-1">Sumber Pengadaan</label>
                        <input type="text" name="acquisition_source" placeholder="Pembelian / Hibah Perpustakaan"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase text-slate-600 mb-1">Harga Pengadaan (Rp)</label>
                        <input type="number" name="acquisition_price" placeholder="75000"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800">
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-indigo-700">
                        Tambah Eksemplar
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection
