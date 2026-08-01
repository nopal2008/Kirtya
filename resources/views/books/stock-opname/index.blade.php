@extends('layouts.app')

@section('title', 'Stock Opname')
@section('page_title', 'Stock Opname Fisik Buku')

@section('content')
<div class="space-y-6">

    <div>
        <h2 class="text-xl font-bold text-slate-800">Stock Opname &amp; Audit Fisik</h2>
        <p class="text-xs text-slate-500 mt-0.5">Audit jumlah dan kondisi fisik eksemplar buku di dalam perpustakaan.</p>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/80">
            <p class="text-xs font-semibold text-slate-500 uppercase">Total Eksemplar</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stats['total_physical']) }}</p>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/80">
            <p class="text-xs font-semibold text-green-600 uppercase">Kondisi Baik</p>
            <p class="text-2xl font-bold text-green-700 mt-1">{{ number_format($stats['good_condition']) }}</p>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/80">
            <p class="text-xs font-semibold text-amber-600 uppercase">Kondisi Rusak</p>
            <p class="text-2xl font-bold text-amber-700 mt-1">{{ number_format($stats['damaged']) }}</p>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/80">
            <p class="text-xs font-semibold text-red-600 uppercase">Hilang</p>
            <p class="text-2xl font-bold text-red-700 mt-1">{{ number_format($stats['lost']) }}</p>
        </div>
    </div>

    {{-- Audit Table --}}
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-[10px] uppercase text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3">Barcode</th>
                        <th class="px-4 py-3">Buku</th>
                        <th class="px-4 py-3">Kondisi Saat Ini</th>
                        <th class="px-4 py-3">Status Sistem</th>
                        <th class="px-4 py-3 text-right">Update Kondisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($stocks as $st)
                    <tr>
                        <td class="px-4 py-3 font-mono font-bold text-indigo-700">{{ $st->barcode }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $st->book->title ?? '-' }}</td>
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
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('books.stocks.update-status', $st) }}" class="inline-flex gap-1">
                                @csrf
                                @method('PATCH')
                                <select name="condition" class="rounded-lg border border-slate-200 text-[10px] px-2 py-1 bg-slate-50">
                                    <option value="good" {{ $st->condition == 'good' ? 'selected' : '' }}>Baik</option>
                                    <option value="damaged" {{ $st->condition == 'damaged' ? 'selected' : '' }}>Rusak</option>
                                    <option value="lost" {{ $st->condition == 'lost' ? 'selected' : '' }}>Hilang</option>
                                </select>
                                <input type="hidden" name="status" value="{{ $st->status }}">
                                <button type="submit" class="rounded-lg bg-slate-800 px-2 py-1 text-[10px] text-white font-semibold">Simpan</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
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
@endsection
