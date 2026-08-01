@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page_title', 'Pengaturan Profil Saya')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <h2 class="text-xl font-bold text-slate-800">Profil Saya</h2>
        <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi diri dan kata sandi akun Anda.</p>
    </div>

    {{-- Info Diri Form --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/80">
        <h3 class="text-sm font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Informasi Diri</h3>
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">ID Anggota</label>
                <input type="text" value="{{ $user->member_id ?? '-' }}" disabled
                       class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3.5 py-2.5 text-xs text-slate-500 font-mono font-bold">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Nama Lengkap *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 font-medium">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Email</label>
                <input type="email" value="{{ $user->email }}" disabled
                       class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3.5 py-2.5 text-xs text-slate-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Nomor Telepon / WA</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Alamat Lengkap</label>
                <textarea name="address" rows="3" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">{{ old('address', $user->address) }}</textarea>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700">
                    Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>

    {{-- Ganti Password Form --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/80">
        <h3 class="text-sm font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Ganti Kata Sandi</h3>
        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Kata Sandi Saat Ini *</label>
                <input type="password" name="current_password" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Kata Sandi Baru *</label>
                <input type="password" name="password" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">Konfirmasi Kata Sandi Baru *</label>
                <input type="password" name="password_confirmation" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="rounded-xl bg-slate-800 px-5 py-2.5 text-xs font-semibold text-white hover:bg-slate-900">
                    Perbarui Kata Sandi
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
