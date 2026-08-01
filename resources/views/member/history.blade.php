@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')
@section('page_title', 'Riwayat Peminjaman Buku Saya')

@section('content')
<div class="space-y-6">

    <div>
        <h2 class="text-xl font-bold text-slate-800">Riwayat Peminjaman Saya</h2>
        <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh transaksi peminjaman buku yang pernah Anda lakukan.</p>
    </div>

    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 border-b border-slate-100 text-[10px] uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5">Kode Transaksi</th>
                        <th class="px-5 py-3.5">Buku</th>
                        <th class="px-5 py-3.5">Tanggal Pinjam</th>
                        <th class="px-5 py-3.5">Jatuh Tempo</th>
                        <th class="px-5 py-3.5">Tanggal Kembali</th>
                        <th class="px-5 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $trx)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-5 py-3.5 font-mono font-bold text-indigo-700">
                            {{ $trx->transaction_code }}
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-slate-800">
                            {{ $trx->bookStock->book->title ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">
                            {{ $trx->borrow_date->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3.5 font-semibold {{ $trx->due_date->isPast() && $trx->status !== 'returned' ? 'text-red-600' : 'text-slate-700' }}">
                            {{ $trx->due_date->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">
                            {{ $trx->return_date ? $trx->return_date->format('d M Y') : '-' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-block rounded-full px-2.5 py-0.5 text-[10px] font-semibold {{ $trx->status_badge_class }}">
                                {{ $trx->status_label }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-slate-400">Belum ada riwayat peminjaman.</td>
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
