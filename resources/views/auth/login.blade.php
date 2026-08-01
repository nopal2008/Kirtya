@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="flex min-h-[80vh] items-center justify-center">
        <div class="w-full max-w-md">

            {{-- Logo --}}
            <div class="mb-8 text-center">
                <div
                    class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 shadow-xl shadow-indigo-500/30">
                    <i class="fa-solid fa-book-open text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">SIPerpus</h1>
                <p class="mt-1 text-sm text-slate-500">Masuk untuk mengakses sistem perpustakaan</p>
            </div>

            {{-- Login Card --}}
            <div class="rounded-2xl bg-white shadow-xl ring-1 ring-slate-200 p-8">

                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5 flex-shrink-0"></i>
                            <div>
                                <p class="text-sm font-medium text-red-800">Terjadi kesalahan:</p>
                                <ul class="mt-1 text-xs text-red-700 list-disc list-inside space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email"
                            class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                <i class="fa-regular fa-envelope text-slate-400 text-sm"></i>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autofocus autocomplete="username" placeholder="nama@sekolah.sch.id"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password"
                            class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                <i class="fa-solid fa-lock text-slate-400 text-sm"></i>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                placeholder="Masukkan kata sandi"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all">
                        </div>
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" id="remember"
                                class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs text-slate-600">Ingat saya</span>
                        </label>
                        <a href="#"
                            class="text-xs font-medium text-indigo-600 hover:text-indigo-700 transition-colors">
                            Lupa kata sandi?
                        </a>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                        class="w-full rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/30 hover:from-indigo-700 hover:to-blue-700 active:scale-[0.98] transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Masuk ke Sistem
                    </button>
                </form>

            </div>

            <p class="mt-6 text-center text-xs text-slate-400">
                Sistem Informasi Perpustakaan &mdash; Hak Akses Terbatas
            </p>

        </div>
    </div>
@endsection
