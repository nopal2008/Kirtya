@extends('layouts.app')

@section('title', 'Scan Barcode Buku')
@section('page_title', 'Scan Barcode Buku')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200/80">
            <div class="max-w-3xl mx-auto">
                <div class="mb-6">
                    <p class="text-xs uppercase tracking-wider text-slate-400">Scan Barcode Buku</p>
                    <h1 class="text-2xl font-bold text-slate-900">Pindai atau masukkan kode barcode buku</h1>
                    <p class="mt-2 text-sm text-slate-600">Gunakan scanner barcode USB atau ketik langsung nilai barcode lalu
                        tekan Enter.</p>
                </div>

                @if ($notFound)
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                        Barcode "<span class="font-mono">{{ $barcode }}</span>" tidak ditemukan. Pastikan barcode benar
                        atau coba lagi.
                    </div>
                @endif

                <form method="GET" action="{{ route('opac.scan') }}" class="space-y-4">
                    <div>
                        <label for="barcode" class="block text-sm font-semibold text-slate-700 mb-2">Kode Barcode</label>
                        <input id="barcode" name="barcode" type="text" value="{{ old('barcode', $barcode) }}"
                            placeholder="Contoh: 1234567890123"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                            autocomplete="off" autofocus>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-indigo-700">
                            <i class="fa-solid fa-barcode mr-2"></i> Cari Buku dengan Barcode
                        </button>
                        <a href="{{ route('opac.index') }}"
                            class="text-sm font-semibold text-slate-600 hover:text-slate-900">Kembali ke katalog</a>
                    </div>
                </form>

                <div class="mt-8 rounded-3xl bg-slate-50 p-6 text-sm text-slate-600">
                    <p class="font-semibold text-slate-800">Tips:</p>
                    <ul class="mt-3 list-disc space-y-2 pl-5">
                        <li>Scanner barcode biasanya mengirim nilai sebagai teks dan menekan Enter otomatis.</li>
                        <li>Jika tidak otomatis, tekan Enter setelah memindai.</li>
                        <li>Hanya barcode yang sudah terdaftar di sistem yang dapat ditemukan.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
