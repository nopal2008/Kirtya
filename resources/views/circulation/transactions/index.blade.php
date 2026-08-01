@extends('layouts.app')

@section('title', 'Daftar Transaksi Sirkulasi')
@section('page_title', 'Riwayat Seluruh Transaksi Sirkulasi')

@section('content')
<div class="space-y-6">

    <div>
        <h2 class="text-xl font-bold text-slate-800">Daftar Transaksi Sirkulasi</h2>
        <p class="text-xs text-slate-500 mt-0.5">Semua riwayat peminjaman, pengembalian, dan booking di perpustakaan.</p>
    </div>

    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 border-b border-slate-100 text-[10px] uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5">Kode Transaksi</th>
                        <th class="px-5 py-3.5">Peminjam</th>
                        <th class="px-5 py-3.5">Buku</th>
                        <th class="px-5 py-3.5">Tgl Pinjam</th>
                        <th class="px-5 py-3.5">Jatuh Tempo</th>
                        <th class="px-5 py-3.5">Tgl Kembali</th>
                        <th class="px-5 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $trx)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-5 py-3.5 font-mono font-bold text-indigo-700">{{ $trx->transaction_code }}</td>
                        <td class="px-5 py-3.5">
                            <p class="font-bold text-slate-800">{{ $trx->user->name ?? '-' }}</p>
                            <p class="text-[10px] text-slate-400 font-mono">{{ $trx->user->member_id ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $trx->bookStock->book->title ?? '-' }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $trx->borrow_date->format('d M Y') }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $trx->due_date->format('d M Y') }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $trx->return_date ? $trx->return_date->format('d M Y') : '-' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-block rounded-full px-2.5 py-0.5 text-[10px] font-semibold {{ $trx->status_badge_class }}">
                                {{ $trx->status_label }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-8 text-center text-slate-400">Tidak ada riwayat transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($transactions->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
