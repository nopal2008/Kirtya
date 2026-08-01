@extends('layouts.app')

@section('title', 'Buku Tamu')
@section('page_title', 'Buku Tamu Perpustakaan')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Buku Tamu Perpustakaan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pencatatan statistik kunjungan harian siswa &amp; tamu umum.</p>
        </div>
        <div class="rounded-2xl bg-indigo-50 px-4 py-2 text-xs font-bold text-indigo-700">
            Total Kunjungan Hari Ini: {{ $todayVisitorsCount }} Pengunjung
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Table --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/80">
                <form method="GET" action="{{ route('circulation.visitors.index') }}">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama pengunjung atau instansi..."
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800">
                </form>
            </div>

            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 border-b border-slate-100 text-[10px] uppercase text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Waktu Masuk</th>
                                <th class="px-4 py-3">Nama Pengunjung</th>
                                <th class="px-4 py-3">Instansi / Kelas</th>
                                <th class="px-4 py-3">Tujuan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($visitors as $v)
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-4 py-3 font-mono text-slate-500">
                                    {{ $v->check_in_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-800">
                                    {{ $v->user->name ?? $v->visitor_name }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    {{ $v->institution ?? 'Siswa Internal' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700">
                                        {{ ucfirst($v->purpose) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400">Belum ada kunjungan hari ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($visitors->hasPages())
                <div class="p-3 border-t border-slate-100">
                    {{ $visitors->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- Checkin Form --}}
        <div>
            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/80 space-y-4">
                <h3 class="text-sm font-bold text-slate-800">Catat Kunjungan Baru</h3>
                <form method="POST" action="{{ route('circulation.visitors.store') }}" class="space-y-3">
                    @csrf

                    <div>
                        <label class="block text-[11px] font-semibold uppercase text-slate-600 mb-1">Tipe Pengunjung *</label>
                        <select name="is_member" id="is_member" onchange="toggleVisitorType(this.value)" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800">
                            <option value="1">Siswa Terdaftar (Anggota)</option>
                            <option value="0">Tamu Umum (Luar)</option>
                        </select>
                    </div>

                    <div id="member_select_wrapper">
                        <label class="block text-[11px] font-semibold uppercase text-slate-600 mb-1">Pilih Siswa *</label>
                        <select name="user_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800">
                            @foreach ($members as $m)
                                <option value="{{ $m->id }}">{{ $m->member_id }} &mdash; {{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="guest_name_wrapper" class="hidden">
                        <label class="block text-[11px] font-semibold uppercase text-slate-600 mb-1">Nama Tamu *</label>
                        <input type="text" name="visitor_name" placeholder="Nama Lengkap Tamu"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase text-slate-600 mb-1">Asal Instansi / Kelas</label>
                        <input type="text" name="institution" placeholder="Kelas XII IPA 1 / Instansi Diterima"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase text-slate-600 mb-1">Tujuan Kunjungan *</label>
                        <select name="purpose" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800">
                            <option value="reading">Membaca Buku</option>
                            <option value="borrowing">Meminjam Buku</option>
                            <option value="returning">Mengembalikan Buku</option>
                            <option value="studying">Belajar / Mengerjakan Tugas</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-indigo-700">
                        Simpan Kunjungan
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

<script>
function toggleVisitorType(val) {
    const memberWrap = document.getElementById('member_select_wrapper');
    const guestWrap = document.getElementById('guest_name_wrapper');
    if (val === '1') {
        memberWrap.classList.remove('hidden');
        guestWrap.classList.add('hidden');
    } else {
        memberWrap.classList.add('hidden');
        guestWrap.classList.remove('hidden');
    }
}
</script>
@endsection
