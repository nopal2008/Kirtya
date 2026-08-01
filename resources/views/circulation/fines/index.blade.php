@extends('layouts.app')

@section('title', 'Pembayaran Denda')
@section('page_title', 'Manajemen & Pembayaran Denda')

@section('content')
<div class="space-y-6">

    <div>
        <h2 class="text-xl font-bold text-slate-800">Manajemen Denda Anggota</h2>
        <p class="text-xs text-slate-500 mt-0.5">Proses penerimaan pembayaran tunai denda atau pembebasan (waive) denda.</p>
    </div>

    {{-- Filter --}}
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/80">
        <form method="GET" action="{{ route('circulation.fines.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari Nama Anggota atau ID Anggota..."
                   class="w-full max-w-sm rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800">
            <select name="status" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800">
                <option value="">-- Semua Status --</option>
                <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                <option value="waived" {{ request('status') == 'waived' ? 'selected' : '' }}>Dibebaskan</option>
            </select>
            <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-xs font-semibold text-white">Filter</button>
        </form>
    </div>

    {{-- Fines Table --}}
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 border-b border-slate-100 text-[10px] uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5">Anggota</th>
                        <th class="px-5 py-3.5">Jenis Denda</th>
                        <th class="px-5 py-3.5">Detail Keterlambatan</th>
                        <th class="px-5 py-3.5">Nominal Denda</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi Pembayaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($fines as $fine)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-5 py-3.5">
                            <p class="font-bold text-slate-800">{{ $fine->user->name ?? '-' }}</p>
                            <p class="text-[10px] text-slate-400 font-mono">{{ $fine->user->member_id ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="font-semibold text-slate-700">{{ $fine->type_label }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">
                            @if ($fine->type === 'overdue')
                                {{ $fine->overdue_days }} hari x Rp {{ number_format($fine->daily_rate, 0, ',', '.') }}
                            @else
                                Denda kondisi fisik
                            @endif
                        </td>
                        <td class="px-5 py-3.5 font-bold text-rose-600">
                            Rp {{ number_format($fine->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-block rounded-full px-2.5 py-0.5 text-[10px] font-semibold {{ $fine->payment_status_badge_class }}">
                                {{ $fine->payment_status_label }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if ($fine->payment_status === 'unpaid')
                            <div class="flex items-center justify-end gap-1.5">
                                <form method="POST" action="{{ route('circulation.fines.process', $fine) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="pay">
                                    <button type="submit" class="rounded-xl bg-green-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-green-700">
                                        Bayar Lunas
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('circulation.fines.process', $fine) }}" onsubmit="const r = prompt('Alasan pembebasan denda:'); if(r){ this.waived_reason.value = r; return true; } return false;">
                                    @csrf
                                    <input type="hidden" name="action" value="waive">
                                    <input type="hidden" name="waived_reason" value="">
                                    <button type="submit" class="rounded-xl bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-600">
                                        Bebaskan
                                    </button>
                                </form>
                            </div>
                            @else
                            <span class="text-[10px] text-slate-400 font-mono">Diproses: {{ $fine->paid_at ? $fine->paid_at->format('d/m/Y') : '-' }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-slate-400">Tidak ada catatan denda.</td>
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
