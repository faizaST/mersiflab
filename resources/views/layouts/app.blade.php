<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistem Monitoring Pelatihan MersifLab') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },

                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    },

                    boxShadow: {
                        soft: '0 4px 20px rgba(15, 23, 42, 0.05)',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- FontAwesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin="" />

    <!-- DataTables CSS -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">

    <style>
        [x-cloak] {
            display: none !important;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 30%, #faf5ff 60%, #fdf2f8 100%);
            min-height: 100vh;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background-color: #e0f2fe;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #38bdf8, #818cf8);
            border-radius: 999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #0ea5e9, #6366f1);
        }

        /* Background Blur Shapes */
        .bg-shape {
            position: absolute;
            border-radius: 9999px;
            filter: blur(100px);
            opacity: 0.35;
            pointer-events: none;
            z-index: 0;
        }

        .shape-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, #bae6fd, #7dd3fc);
            top: -150px;
            left: -100px;
        }

        .shape-2 {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, #ddd6fe, #c4b5fd);
            bottom: -120px;
            right: -80px;
        }

        .shape-3 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, #fbcfe8, #f9a8d4);
            top: 40%;
            right: 15%;
            opacity: 0.2;
        }

        /* Glass Header */
        .glass-header {
            background: rgba(255, 255, 255, 0.80);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(186, 230, 253, 0.6);
            box-shadow: 0 2px 20px rgba(14, 165, 233, 0.08);
        }

        /* Card Style */
        .modern-card {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(186, 230, 253, 0.5);
            border-radius: 1.5rem;
            box-shadow: 0 4px 24px rgba(14, 165, 233, 0.08);
            backdrop-filter: blur(8px);
        }

        /* Sidebar nav list reset */
        #sidebar-nav li {
            margin: 0 !important;
            padding: 0 !important;
        }
        #sidebar-nav li > div {
            margin: 0 !important;
            line-height: 1 !important;
        }

        /* ── Sinkronisasi warna tombol & elemen dengan background ── */

        /* Primary button: bg-sky-600, bg-sky-700 → gradient biru-ungu pastel */
        [class~="bg-sky-600"],
        [class~="bg-sky-700"],
        [class~="bg-sky-500"] {
            background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%) !important;
            color: #fff !important;
            box-shadow: 0 4px 14px rgba(56, 189, 248, 0.30) !important;
        }
        [class~="bg-sky-600"]:hover,
        [class~="bg-sky-700"]:hover,
        [class~="bg-sky-500"]:hover {
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%) !important;
            box-shadow: 0 6px 18px rgba(56, 189, 248, 0.45) !important;
        }

        /* Focus ring selaras */
        [class*="focus:ring-sky"] {
            --tw-ring-color: rgba(129, 140, 248, 0.4) !important;
        }

        /* Teks sky-600 → indigo-500 agar selaras */
        .text-sky-600 { color: #6366f1 !important; }
        .hover\:text-sky-600:hover { color: #6366f1 !important; }
        .text-sky-500 { color: #38bdf8 !important; }
        .text-sky-700 { color: #0369a1 !important; }

        /* Border sky */
        .border-sky-300 { border-color: #a5b4fc !important; }
        .ring-sky-300   { --tw-ring-color: rgba(165, 180, 252, 0.5) !important; }

        /* Sidebar active item */
        .sidebar-active {
            background: linear-gradient(135deg, #e0f2fe 0%, #ede9fe 100%) !important;
            color: #6366f1 !important;
        }
    </style>

    @stack('styles')
</head>

@php
    $isPeserta = auth()->user()?->role === 'peserta';
@endphp

<body class="h-full font-sans antialiased text-slate-700"
      x-data="{ sidebarOpen: true, userMenuOpen: false }">

@if($isPeserta)

    {{-- ======================= --}}
    {{-- PESERTA LEARNING PORTAL --}}
    {{-- ======================= --}}

    <div class="flex flex-col h-screen overflow-hidden relative">

        <div class="bg-shape shape-1"></div>
        <div class="bg-shape shape-2"></div>
        <div class="bg-shape shape-3"></div>

        {{-- Header --}}
        <header class="shrink-0 z-40 glass-header" x-data="{ mobileMenuOpen: false }">

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="flex items-center justify-between h-16">

                    {{-- Logo + Menu --}}
                    <div class="flex items-center gap-6">

                        <a href="{{ route('peserta.dashboard') }}"
                           class="flex items-center transition hover:scale-105">

                            <img src="{{ asset('img/logo-mersfilab.png') }}"
                                 class="h-10 object-contain"
                                 alt="MersifLab Logo">
                        </a>

                        {{-- Desktop Menu --}}
                        <nav class="hidden md:flex items-center gap-2">

                            <a href="{{ route('peserta.dashboard') }}"
                               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200
                               {{ request()->routeIs('peserta.dashboard')
                                    ? 'bg-sky-500 text-white shadow-soft'
                                    : 'text-slate-600 hover:bg-sky-50 hover:text-sky-600' }}">
                                <i class="fa-solid fa-house mr-1.5 text-xs"></i>Dashboard
                            </a>

                            <a href="{{ route('peserta.pelatihan.index') }}"
                               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200
                               {{ request()->routeIs('peserta.pelatihan.*')
                                    ? 'bg-sky-500 text-white shadow-soft'
                                    : 'text-slate-600 hover:bg-sky-50 hover:text-sky-600' }}">
                                <i class="fa-solid fa-graduation-cap mr-1.5 text-xs"></i>Pelatihan
                            </a>

                            <a href="{{ route('peserta.aktivitas.index') }}"
                               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200
                               {{ request()->routeIs('peserta.aktivitas.*')
                                    ? 'bg-sky-500 text-white shadow-soft'
                                    : 'text-slate-600 hover:bg-sky-50 hover:text-sky-600' }}">
                                <i class="fa-solid fa-book-open mr-1.5 text-xs"></i>Aktivitas
                            </a>

                            <a href="{{ route('peserta.riwayat.index') }}"
                               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200
                               {{ request()->routeIs('peserta.riwayat.*')
                                    ? 'bg-sky-500 text-white shadow-soft'
                                    : 'text-slate-600 hover:bg-sky-50 hover:text-sky-600' }}">
                                <i class="fa-solid fa-clock-rotate-left mr-1.5 text-xs"></i>Riwayat
                            </a>

                        </nav>
                    </div>

                    {{-- Right: Profile + Hamburger --}}
                    <div class="flex items-center gap-2">

                        {{-- Hamburger (mobile only) --}}
                        <button type="button"
                                @click="mobileMenuOpen = !mobileMenuOpen"
                                class="md:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 transition-all">
                            <i x-show="!mobileMenuOpen" class="fa-solid fa-bars text-lg"></i>
                            <i x-show="mobileMenuOpen" x-cloak class="fa-solid fa-xmark text-lg"></i>
                        </button>

                        {{-- Profile Dropdown --}}
                        <div class="relative">

                            <button type="button"
                                    @click="userMenuOpen = !userMenuOpen"
                                    @click.away="userMenuOpen = false"
                                    class="flex items-center gap-2 rounded-2xl px-2 py-1.5 hover:bg-slate-100 transition-all">

                                <img class="w-9 h-9 rounded-2xl object-cover border-2 border-sky-100"
                                     src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->name ?? 'Guest') }}&background=0EA5E9&color=fff&bold=true"
                                     alt="Avatar">

                                <div class="hidden sm:block text-left">
                                    <p class="text-sm font-semibold text-slate-700">
                                        {{ auth()->user()?->name ?? 'Guest' }}
                                    </p>
                                    <p class="text-[11px] uppercase tracking-wider text-slate-400">
                                        Peserta
                                    </p>
                                </div>

                                <i class="fa-solid fa-chevron-down text-xs text-slate-400 hidden sm:block"></i>
                            </button>

                            {{-- Dropdown --}}
                            <div x-show="userMenuOpen"
                                 x-cloak
                                 x-transition
                                 class="absolute right-0 w-60 mt-3 bg-white rounded-2xl border border-slate-100 shadow-2xl overflow-hidden z-50">

                                <div class="px-5 py-4 bg-sky-50 border-b border-slate-100">
                                    <p class="text-sm font-bold text-slate-800 truncate">
                                        {{ auth()->user()?->name }}
                                    </p>
                                    <p class="text-xs text-slate-500 truncate mt-1">
                                        {{ auth()->user()?->email }}
                                    </p>
                                </div>

                                <div class="p-2">
                                    <a href="{{ route('peserta.profil.edit') }}"
                                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-slate-600 hover:bg-sky-50 hover:text-sky-600 transition-all">
                                        <i class="fa-solid fa-user-circle text-slate-400"></i>
                                        Profil Saya
                                    </a>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-red-500 hover:bg-red-50 transition-all">
                                            <i class="fa-solid fa-right-from-bracket text-red-400"></i>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            {{-- Mobile Menu Drawer --}}
            <div x-show="mobileMenuOpen"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="md:hidden border-t border-sky-100/60 bg-white/95 backdrop-blur-xl">

                <nav class="max-w-7xl mx-auto px-4 py-3 flex flex-col gap-1">

                    <a href="{{ route('peserta.dashboard') }}"
                       @click="mobileMenuOpen = false"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all
                       {{ request()->routeIs('peserta.dashboard')
                            ? 'text-white'
                            : 'text-slate-600 hover:bg-sky-50 hover:text-sky-600' }}"
                       style="{{ request()->routeIs('peserta.dashboard') ? 'background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%);' : '' }}">
                        <i class="fa-solid fa-house w-5 text-center"></i>
                        Dashboard
                    </a>

                    <a href="{{ route('peserta.pelatihan.index') }}"
                       @click="mobileMenuOpen = false"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all
                       {{ request()->routeIs('peserta.pelatihan.*')
                            ? 'text-white'
                            : 'text-slate-600 hover:bg-sky-50 hover:text-sky-600' }}"
                       style="{{ request()->routeIs('peserta.pelatihan.*') ? 'background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%);' : '' }}">
                        <i class="fa-solid fa-graduation-cap w-5 text-center"></i>
                        Pelatihan
                    </a>

                    <a href="{{ route('peserta.aktivitas.index') }}"
                       @click="mobileMenuOpen = false"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all
                       {{ request()->routeIs('peserta.aktivitas.*')
                            ? 'text-white'
                            : 'text-slate-600 hover:bg-sky-50 hover:text-sky-600' }}"
                       style="{{ request()->routeIs('peserta.aktivitas.*') ? 'background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%);' : '' }}">
                        <i class="fa-solid fa-book-open w-5 text-center"></i>
                        Aktivitas
                    </a>

                    <a href="{{ route('peserta.riwayat.index') }}"
                       @click="mobileMenuOpen = false"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all
                       {{ request()->routeIs('peserta.riwayat.*')
                            ? 'text-white'
                            : 'text-slate-600 hover:bg-sky-50 hover:text-sky-600' }}"
                       style="{{ request()->routeIs('peserta.riwayat.*') ? 'background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%);' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left w-5 text-center"></i>
                        Riwayat
                    </a>

                    <a href="{{ route('peserta.profil.edit') }}"
                       @click="mobileMenuOpen = false"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all
                       {{ request()->routeIs('peserta.profil.*')
                            ? 'text-white'
                            : 'text-slate-600 hover:bg-sky-50 hover:text-sky-600' }}"
                       style="{{ request()->routeIs('peserta.profil.*') ? 'background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%);' : '' }}">
                        <i class="fa-solid fa-user-circle w-5 text-center"></i>
                        Profil
                    </a>

                    <div class="border-t border-slate-100 mt-1 pt-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-red-500 hover:bg-red-50 transition-all">
                                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                                Logout
                            </button>
                        </form>
                    </div>

                </nav>
            </div>

        </header>

        {{-- Main Content --}}
        <main class="flex-1 overflow-y-auto focus:outline-none relative z-10"
              id="peserta-main-content">
            <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
                @yield('content')
            </div>
        </main>

        {{-- Footer --}}
        <footer class="shrink-0 bg-white/70 backdrop-blur-sm border-t border-sky-100/60 py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} MersifLab Learning Portal. All rights reserved.
            </div>
        </footer>
    </div>

@else

    {{-- ======================= --}}
    {{-- ADMIN & MENTOR LAYOUT --}}
    {{-- ======================= --}}

    <div class="flex h-screen overflow-hidden relative">

        <div class="bg-shape shape-1"></div>
        <div class="bg-shape shape-2"></div>
        <div class="bg-shape shape-3"></div>

        {{-- Sidebar --}}
        @include('layouts.sidebar')

        {{-- Main Content --}}
        <div class="flex flex-col flex-1 w-0 overflow-visible relative z-10">

            {{-- Navbar --}}
            @include('layouts.navbar')

            {{-- Content --}}
            <main class="flex-1 overflow-y-auto focus:outline-none"
                  id="main-content">

                <div class="py-6">

                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">

                        @yield('content')

                    </div>
                </div>
            </main>
        </div>
    </div>

@endif

<!-- JQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Leaflet -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    @if(session('success'))
        Toast.fire({
            icon: 'success',
            title: @json(session('success'))
        });
    @endif

    @if(session('error'))
        Toast.fire({
            icon: 'error',
            title: @json(session('error'))
        });
    @endif
</script>

@stack('scripts')

</body>
</html>