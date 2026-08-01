@extends('layouts.app')

@section('title', 'Audit Log')
@section('page_title', 'Audit Log Sistem')

@section('content')
<div class="space-y-6">

    <div>
        <h2 class="text-xl font-bold text-slate-800">Audit Log Aktivitas</h2>
        <p class="text-xs text-slate-500 mt-0.5">Catatan riwayat aksi dan aktivitas pengguna di dalam sistem.</p>
    </div>

    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/80">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari aksi, modul, atau alamat IP..."
                   class="w-full max-w-sm rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
        </form>
    </div>

    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 border-b border-slate-100 text-[11px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5">Waktu</th>
                        <th class="px-5 py-3.5">Pengguna</th>
                        <th class="px-5 py-3.5">Aksi</th>
                        <th class="px-5 py-3.5">Modul Terdampak</th>
                        <th class="px-5 py-3.5">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3.5 text-xs text-slate-500 font-mono">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs font-semibold text-slate-800">{{ $log->user->name ?? 'Sistem' }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-block font-mono text-xs font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-slate-600">
                            {{ $log->model_type ? class_basename($log->model_type) . " #" . $log->model_id : '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-xs font-mono text-slate-500">
                            {{ $log->ip_address ?? '127.0.0.1' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-xs text-slate-400">Belum ada catatan audit log.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($logs->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
