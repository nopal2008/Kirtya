@extends('layouts.app')

@section('title', 'Cetak Barcode')
@section('page_title', 'Cetak Label Barcode Buku')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between no-print">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Cetak Label Barcode</h2>
            <p class="text-xs text-slate-500 mt-0.5">Siapkan label barcode eksemplar buku untuk ditempel pada fisik buku.</p>
        </div>
        <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
            <i class="fa-solid fa-print"></i> Cetak Label (PDF / Print)
        </button>
    </div>

    {{-- Printable Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 print:grid-cols-3 print:gap-3">
        @foreach ($stocks as $st)
        <div class="rounded-xl border-2 border-dashed border-slate-300 bg-white p-3 text-center space-y-1 shadow-sm print:border-slate-800 print:shadow-none">
            <p class="text-[9px] font-extrabold uppercase tracking-widest text-slate-500">Perpustakaan Sekolah</p>
            <p class="text-xs font-bold text-slate-900 line-clamp-1">{{ $st->book->title ?? '-' }}</p>
            
            {{-- Visual Barcode Representation --}}
            <div class="py-2 flex flex-col items-center justify-center">
                <div class="h-10 w-full flex items-center justify-center gap-0.5 bg-slate-900 px-2 rounded-sm text-white">
                    <i class="fa-solid fa-barcode text-3xl tracking-tighter"></i>
                </div>
                <span class="font-mono text-xs font-bold text-slate-800 mt-1">{{ $st->barcode }}</span>
            </div>

            <div class="flex items-center justify-between text-[9px] font-mono text-slate-500 border-t border-slate-100 pt-1">
                <span>NIB: {{ $st->accession_number }}</span>
                <span>DDC: {{ $st->book->dewey_decimal ?? '-' }}</span>
            </div>
        </div>
        @endforeach
    </div>

    @if ($stocks->hasPages())
    <div class="pt-4 no-print">
        {{ $stocks->links() }}
    </div>
    @endif

</div>

<style>
@media print {
    .no-print, header, aside, #sidebar-overlay { display: none !important; }
    main { padding: 0 !important; }
    body { background: white !important; }
}
</style>
@endsection
