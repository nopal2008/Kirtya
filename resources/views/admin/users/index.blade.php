@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Pengguna')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-slate-600">Beranda</a>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-slate-600 font-medium">Pengguna</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Daftar Pengguna Sistem</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data anggota, petugas, dan administrator perpustakaan.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors">
            <i class="fa-solid fa-user-plus text-xs"></i>
            Tambah Pengguna
        </a>
    </div>

    {{-- Filter Card --}}
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/80">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama, email, atau ID Anggota..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>
            <div>
                <select name="role" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">-- Semua Hak Akses (Role) --</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $r->name)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <select name="status" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">-- Semua Status --</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                </select>
                <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-900 transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 border-b border-slate-100 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5">ID Anggota</th>
                        <th class="px-5 py-3.5">Nama &amp; Email</th>
                        <th class="px-5 py-3.5">Hak Akses</th>
                        <th class="px-5 py-3.5">Telepon</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3.5">
                            <span class="font-mono text-xs font-semibold text-slate-700 bg-slate-100 px-2 py-1 rounded-lg">
                                {{ $user->member_id ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-xs font-bold text-white shadow">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-800">{{ $user->name }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-block rounded-full bg-indigo-50 px-2.5 py-0.5 text-[11px] font-semibold text-indigo-700">
                                {{ ucfirst(str_replace('_', ' ', $user->getRoleNames()->first() ?? 'Siswa')) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-slate-600">
                            {{ $user->phone ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5">
                            @if ($user->status === 'active')
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-[11px] font-semibold text-green-800">
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-600"></span> Aktif
                                </span>
                            @elseif ($user->status === 'suspended')
                                <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-[11px] font-semibold text-red-800">
                                    <span class="h-1.5 w-1.5 rounded-full bg-red-600"></span> Ditangguhkan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Non-Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-indigo-600 transition-colors">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </a>
                                @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-xs text-slate-400">Tidak ada pengguna ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
