@extends('layouts.app')

@section('title', 'Peminjaman Baru')
@section('page_title', 'Transaksi Peminjaman Buku Baru')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    {{-- HEADER --}}
    <div>
        <h2 class="text-xl font-bold text-slate-800">Form Peminjaman Buku</h2>
        <p class="text-xs text-slate-500 mt-0.5">Proses transaksi peminjaman eksemplar buku oleh anggota.</p>
    </div>

    {{-- =====================================================================
         FORM PEMINJAMAN MANUAL OLEH PETUGAS
    ====================================================================== --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/80">
        <form method="POST" action="{{ route('circulation.borrow.store') }}" class="space-y-4">
            @csrf

            {{-- Anggota Peminjam --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Pilih Anggota (Siswa) *</label>
                <select name="user_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">-- Cari Nama atau ID Anggota --</option>
                    @foreach ($members as $m)
                        <option value="{{ $m->id }}" {{ old('user_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->member_id }} &mdash; {{ $m->name }}
                        </option>
                    @endforeach
                </select>
                @error('user_id') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Eksemplar Buku --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Pilih Eksemplar Buku (Barcode) *</label>
                <select name="book_stock_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">-- Scan / Pilih Barcode Eksemplar Tersedia --</option>
                    @foreach ($availableBooks as $st)
                        <option value="{{ $st->id }}" {{ old('book_stock_id') == $st->id ? 'selected' : '' }}>
                            [{{ $st->barcode }}] {{ $st->book->title }} (DDC: {{ $st->book->dewey_decimal ?? '-' }})
                        </option>
                    @endforeach
                </select>
                @error('book_stock_id') <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tanggal Pinjam & Informasi Durasi --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Tanggal Peminjaman *</label>
                    <input type="date" name="borrow_date" value="{{ old('borrow_date', date('Y-m-d')) }}" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Ketentuan Durasi</label>
                    <div class="rounded-xl bg-indigo-50 p-2.5 text-xs text-indigo-800 font-semibold">
                        Maks. {{ $setting?->max_borrow_days ?? 7 }} hari (Jatuh tempo otomatis)
                    </div>
                </div>
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Catatan Peminjaman</label>
                <textarea name="notes" rows="2" placeholder="Catatan tambahan dari petugas..."
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="submit" class="rounded-xl bg-indigo-600 px-6 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 active:scale-95 transition-all">
                    <i class="fa-solid fa-check mr-1.5"></i> Proses Peminjaman
                </button>
            </div>
        </form>
    </div>

    {{-- =====================================================================
         DAFTAR BOOKING SISWA MENUNGGU PERSETUJUAN PETUGAS
    ====================================================================== --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-100 text-amber-600 text-xs">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </span>
                    Booking Siswa Menunggu Persetujuan
                </h3>
                <p class="text-xs text-slate-500 mt-0.5 ml-9">
                    Siswa yang melakukan booking mandiri dari akun OPAC mereka dan meminta persetujuan petugas.
                </p>
            </div>
            @if($pendingBookings->count() > 0)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700 border border-amber-200">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $pendingBookings->count() }} Menunggu
                </span>
            @endif
        </div>

        @if($pendingBookings->isEmpty())
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 p-10 text-center">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                    <i class="fa-solid fa-inbox text-xl"></i>
                </div>
                <p class="text-sm font-medium text-slate-500">Tidak ada booking siswa yang menunggu persetujuan saat ini.</p>
            </div>
        @else
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-4 py-3 text-left font-semibold text-slate-600 uppercase tracking-wider">Kode Booking</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600 uppercase tracking-wider">Siswa</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600 uppercase tracking-wider">Buku</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600 uppercase tracking-wider">Tgl Booking</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600 uppercase tracking-wider">Status Denda</th>
                                <th class="px-4 py-3 text-center font-semibold text-slate-600 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pendingBookings as $booking)
                                @php
                                    $siswa = $booking->user;
                                    $hasFine = $siswa?->has_any_fine ?? false;
                                    $fineAmount = $siswa?->unpaid_fines_total ?? 0;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors {{ $hasFine ? 'bg-red-50/30' : '' }}">
                                    <td class="px-4 py-3">
                                        <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg">
                                            {{ $booking->transaction_code }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div>
                                            <p class="font-semibold text-slate-800">{{ $siswa?->name ?? '-' }}</p>
                                            <p class="text-slate-400 text-[11px]">{{ $siswa?->member_id ?? '' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-slate-700 max-w-44 truncate">{{ $booking->bookStock?->book?->title ?? '-' }}</p>
                                        <p class="text-slate-400 text-[11px]">Barcode: {{ $booking->bookStock?->barcode ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">
                                        {{ $booking->created_at?->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($hasFine)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-red-100 border border-red-200 px-2.5 py-1 text-[11px] font-bold text-red-700">
                                                <i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
                                                Ada Denda: Rp {{ number_format($fineAmount, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 border border-emerald-200 px-2.5 py-1 text-[11px] font-bold text-emerald-700">
                                                <i class="fa-solid fa-circle-check text-[9px]"></i>
                                                Bersih
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-2">
                                            @if($hasFine)
                                                {{-- Tidak bisa disetujui jika ada denda --}}
                                                <button disabled
                                                    title="Siswa masih memiliki denda, tidak dapat disetujui"
                                                    class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-[11px] font-semibold text-slate-400 cursor-not-allowed">
                                                    <i class="fa-solid fa-ban text-[9px]"></i> Setujui
                                                </button>
                                            @else
                                                <form method="POST" action="{{ route('circulation.borrow.approve', $booking) }}" onsubmit="return confirm('Setujui booking {{ $booking->transaction_code }} untuk {{ $siswa?->name }}?')">
                                                    @csrf
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-[11px] font-semibold text-white hover:bg-emerald-700 active:scale-95 transition-all shadow-sm">
                                                        <i class="fa-solid fa-check text-[9px]"></i> Setujui
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Tombol Tolak --}}
                                            <button type="button"
                                                onclick="openRejectModal({{ $booking->id }}, '{{ $booking->transaction_code }}', '{{ addslashes($siswa?->name) }}')"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-red-100 px-3 py-1.5 text-[11px] font-semibold text-red-700 hover:bg-red-200 active:scale-95 transition-all border border-red-200">
                                                <i class="fa-solid fa-xmark text-[9px]"></i> Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

</div>

{{-- MODAL TOLAK BOOKING --}}
<div id="reject-modal" class="fixed inset-0 z-50 flex items-center justify-center px-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl ring-1 ring-slate-200 p-6 space-y-4">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 text-red-600">
                <i class="fa-solid fa-xmark-circle"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 text-sm">Tolak Booking</h4>
                <p id="reject-modal-subtitle" class="text-xs text-slate-500"></p>
            </div>
        </div>
        <form id="reject-form" method="POST" action="">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Penolakan</label>
                    <textarea name="reject_reason" rows="3" required placeholder="Jelaskan alasan penolakan booking ini..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20"></textarea>
                </div>
                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" onclick="closeRejectModal()"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="rounded-xl bg-red-600 px-4 py-2 text-xs font-semibold text-white hover:bg-red-700">
                        <i class="fa-solid fa-xmark mr-1"></i> Tolak Booking
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openRejectModal(bookingId, code, name) {
    document.getElementById('reject-modal-subtitle').textContent = 'Kode: ' + code + ' — ' + name;
    document.getElementById('reject-form').action = '/circulation/borrow/' + bookingId + '/reject';
    document.getElementById('reject-modal').classList.remove('hidden');
}
function closeRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
}
</script>
@endpush

@endsection
