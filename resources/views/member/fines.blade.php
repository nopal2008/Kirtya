@extends('layouts.app')

@section('title', 'Info Denda Saya')
@section('page_title', 'Informasi Denda Saya')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Informasi Denda Saya</h2>
            <p class="text-xs text-slate-500 mt-0.5">Rincian tagihan denda akibat keterlambatan atau kondisi buku.</p>
        </div>

        <div class="rounded-2xl p-4 shadow-sm ring-1 ring-slate-200/80 {{ $totalUnpaid > 0 ? 'bg-rose-50 text-rose-800' : 'bg-green-50 text-green-800' }}">
            <span class="text-[10px] uppercase font-bold tracking-wider">Total Denda Belum Dibayar</span>
            <p class="text-xl font-extrabold mt-0.5">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 border-b border-slate-100 text-[10px] uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5">Buku / Transaksi</th>
                        <th class="px-5 py-3.5">Jenis Denda</th>
                        <th class="px-5 py-3.5">Keterlambatan</th>
                        <th class="px-5 py-3.5">Nominal Tagihan</th>
                        <th class="px-5 py-3.5">Status Pembayaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($fines as $fine)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-5 py-3.5">
                            <p class="font-bold text-slate-800">{{ $fine->transaction->bookStock->book->title ?? '-' }}</p>
                            <p class="text-[10px] text-slate-400 font-mono">{{ $fine->transaction->transaction_code ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-slate-700">{{ $fine->type_label }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $fine->overdue_days > 0 ? $fine->overdue_days . ' hari' : '-' }}</td>
                        <td class="px-5 py-3.5 font-bold text-rose-600">Rp {{ number_format($fine->amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-block rounded-full px-2.5 py-0.5 text-[10px] font-semibold {{ $fine->payment_status_badge_class }}">
                                {{ $fine->payment_status_label }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-slate-400">Tidak ada catatan denda. Selamat, Anda anggota yang disiplin!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($fines->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $fines->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
