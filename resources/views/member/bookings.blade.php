@extends('layouts.app')

@section('title', 'Booking Buku Saya')
@section('page_title', 'Reservasi & Booking Buku Saya')

@section('content')
<div class="space-y-6">

    {{-- HEADER + NOTIF DENDA --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Booking Buku Saya</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar reservasi stok buku online melalui katalog OPAC.</p>
        </div>
        <a href="{{ route('opac.index') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 shadow-sm">
            <i class="fa-solid fa-magnifying-glass"></i> Cari Buku di OPAC
        </a>
    </div>

    {{-- PERINGATAN JIKA ADA DENDA --}}
    @if(auth()->user()->has_any_fine)
        <div class="flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm">
            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600 mt-0.5">
                <i class="fa-solid fa-circle-exclamation text-base"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-red-800">Akun Diblokir dari Booking & Peminjaman</p>
                <p class="text-xs text-red-600 mt-0.5">
                    Kamu memiliki tunggakan denda sebesar
                    <strong>Rp {{ number_format(auth()->user()->unpaid_fines_total, 0, ',', '.') }}</strong>.
                    Harap lunasi di loket petugas perpustakaan sebelum dapat melakukan booking atau meminjam buku baru.
                </p>
                <a href="{{ route('member.fines') }}" class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-red-700 hover:text-red-900 underline">
                    <i class="fa-solid fa-arrow-right text-[9px]"></i> Lihat Detail Denda
                </a>
            </div>
        </div>
    @endif

    {{-- TABEL BOOKING --}}
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 border-b border-slate-100 text-[10px] uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5">Kode Booking</th>
                        <th class="px-5 py-3.5">Buku</th>
                        <th class="px-5 py-3.5">Tanggal Booking</th>
                        <th class="px-5 py-3.5">Batas Pengambilan</th>
                        <th class="px-5 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($bookings as $bkg)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-5 py-3.5 font-mono font-bold text-indigo-700">{{ $bkg->transaction_code }}</td>
                        <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $bkg->bookStock->book->title ?? '-' }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $bkg->borrow_date?->format('d M Y') }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $bkg->booking_expiry ? $bkg->booking_expiry->format('d M Y H:i') : '-' }}</td>
                        <td class="px-5 py-3.5">
                            @if($bkg->status === 'pending_approval')
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 border border-amber-200 px-2.5 py-1 text-[10px] font-bold text-amber-800">
                                    <i class="fa-solid fa-clock text-[8px]"></i>
                                    Menunggu Persetujuan
                                </span>
                            @elseif($bkg->status === 'borrowed')
                                <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 border border-blue-200 px-2.5 py-1 text-[10px] font-bold text-blue-800">
                                    <i class="fa-solid fa-circle-check text-[8px]"></i>
                                    Disetujui / Dipinjam
                                </span>
                            @elseif($bkg->status === 'rejected')
                                <span class="inline-flex items-center gap-1 rounded-full bg-red-100 border border-red-200 px-2.5 py-1 text-[10px] font-bold text-red-800">
                                    <i class="fa-solid fa-xmark text-[8px]"></i>
                                    Ditolak Petugas
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-600">
                                    {{ $bkg->status_label }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-400">
                            <i class="fa-solid fa-bookmark text-2xl mb-2 block text-slate-200"></i>
                            Belum ada reservasi booking buku.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($bookings->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
