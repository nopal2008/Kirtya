@extends('layouts.app')

@section('title', 'Konfigurasi Denda & Peminjaman')
@section('page_title', 'Konfigurasi Denda & Peminjaman')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <h2 class="text-xl font-bold text-slate-800">Pengaturan Denda &amp; Sirkulasi</h2>
        <p class="text-xs text-slate-500 mt-0.5">Konfigurasi tarif denda keterlambatan, kerusakan, kehilangan, serta limit peminjaman buku.</p>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/80">
        <form method="POST" action="{{ route('admin.fines.settings.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Tarif Denda Harian --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Tarif Denda Keterlambatan (per Hari)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-xs text-slate-400 font-semibold">Rp</span>
                        <input type="number" step="500" min="0" name="daily_rate" value="{{ old('daily_rate', $setting->daily_rate) }}" required
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-bold">
                    </div>
                    <p class="text-[11px] text-slate-400">Dikenakan per hari terlambat untuk setiap eksemplar buku.</p>
                </div>

                {{-- Batas Maksimal Hari Pinjam --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Batas Maksimal Durasi Pinjam (Hari)</label>
                    <div class="relative">
                        <input type="number" min="1" max="60" name="max_borrow_days" value="{{ old('max_borrow_days', $setting->max_borrow_days) }}" required
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-bold">
                    </div>
                    <p class="text-[11px] text-slate-400">Durasi peminjaman standar sebelum dianggap terlambat.</p>
                </div>

                {{-- Batas Jumlah Pinjam Buku --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Limit Peminjaman Buku Sekaligus</label>
                    <div class="relative">
                        <input type="number" min="1" max="10" name="max_borrow_limit" value="{{ old('max_borrow_limit', $setting->max_borrow_limit) }}" required
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-bold">
                    </div>
                    <p class="text-[11px] text-slate-400">Maksimal jumlah eksemplar buku yang dipinjam satu siswa.</p>
                </div>

                {{-- Denda Kerusakan --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Denda Kerusakan Buku</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-xs text-slate-400 font-semibold">Rp</span>
                        <input type="number" step="1000" min="0" name="damage_fee" value="{{ old('damage_fee', $setting->damage_fee) }}" required
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-bold">
                    </div>
                    <p class="text-[11px] text-slate-400">Denda tetap untuk fisik buku yang dikembalikan rusak.</p>
                </div>

                {{-- Pengali Denda Kehilangan --}}
                <div class="space-y-1 md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Pengali Denda Kehilangan Buku (x Harga Pengadaan)</label>
                    <div class="relative max-w-xs">
                        <input type="number" step="0.1" min="1" max="10" name="lost_fee_multiplier" value="{{ old('lost_fee_multiplier', $setting->lost_fee_multiplier) }}" required
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-bold">
                    </div>
                    <p class="text-[11px] text-slate-400">Contoh: nilai 2.0 berarti denda hilang = 2x harga pengadaan buku.</p>
                </div>

            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="rounded-xl bg-indigo-600 px-6 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Konfigurasi
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
