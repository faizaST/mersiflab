@php
    $currentRole = auth()->user()?->role ?? 'guest';
    $isAdmin   = $currentRole === 'admin';
    $isMentor  = $currentRole === 'mentor';
    $isPeserta = $currentRole === 'peserta';
@endphp

<div :class="sidebarOpen ? 'w-64' : 'w-20'"
     class="bg-gradient-to-b from-white via-sky-50/60 to-sky-50/80 border-r border-sky-100/80 text-slate-600 transition-all duration-300 ease-in-out flex flex-col shadow-lg shadow-sky-100/50 z-30 relative shrink-0">

    {{-- Brand --}}
    <div class="h-16 flex items-center justify-center border-b border-sky-100/60 overflow-hidden px-4 bg-gradient-to-r from-sky-500/10 to-sky-500/10">
        <a href="/" class="flex items-center justify-center w-full h-full">
            
            {{-- Full Logo --}}
            <div x-show="sidebarOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-x-2"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 class="w-full flex justify-center">

                <img src="{{ asset('img/logo-mersfilab.png') }}"
                     class="h-10 object-contain drop-shadow-sm"
                     alt="MersifLab Logo">
            </div>

            {{-- Mini Logo --}}
            <div x-show="!sidebarOpen"
                 class="w-10 h-10 flex items-center justify-center bg-sky-50 rounded-2xl border border-sky-100">

                <img src="{{ asset('img/logo-mersfilab.png') }}"
                     class="h-8 w-8 object-cover"
                     style="object-position: left center;"
                     alt="MersifLab Icon">
            </div>

        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-3 overflow-x-hidden">
        <ul class="space-y-0.5 px-3" id="sidebar-nav">

            {{-- ── ADMIN ── --}}
            @if($isAdmin)

                <li>
                    <div x-show="sidebarOpen"
                         class="px-3 pt-1 pb-0 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                        Main
                    </div>
                </li>

                @include('layouts.partials.nav-item', [
                    'href' => route('admin.dashboard'),
                    'icon' => 'fa-chart-pie',
                    'label' => 'Dashboard',
                    'active' => request()->routeIs('admin.dashboard')
                ])

                <li>
                    <div x-show="sidebarOpen"
                         class="px-3 pt-9 pb-0 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                        Manajemen
                    </div>
                </li>

                @include('layouts.partials.nav-item', [
                    'href' => route('admin.pelatihan.index'),
                    'icon' => 'fa-chalkboard-user',
                    'label' => 'Pelatihan',
                    'active' => request()->routeIs('admin.pelatihan.*')
                ])

                @include('layouts.partials.nav-item', [
                    'href' => route('admin.kategori.index'),
                    'icon' => 'fa-tags',
                    'label' => 'Kategori Pelatihan',
                    'active' => request()->routeIs('admin.kategori.*')
                ])

                @include('layouts.partials.nav-item', [
                    'href' => route('admin.pendaftaran.index'),
                    'icon' => 'fa-clipboard-list',
                    'label' => 'Pendaftaran',
                    'active' => request()->routeIs('admin.pendaftaran.*')
                ])

                @include('layouts.partials.nav-item', [
                    'href' => route('admin.monitoring.index'),
                    'icon' => 'fa-chart-line',
                    'label' => 'Monitoring',
                    'active' => request()->routeIs('admin.monitoring.*')
                ])

                 @include('layouts.partials.nav-item', [
                    'href' => route('admin.desain-sertifikat.index'),
                    'icon' => 'fa-award',
                    'label' => 'Desain Sertifikat',
                    'active' => request()->routeIs('admin.desain-sertifikat.*')
                ])                

                @include('layouts.partials.nav-item', [
                    'href' => route('admin.sertifikat.index'),
                    'icon' => 'fa-certificate',
                    'label' => 'Sertifikat & Laporan',
                    'active' => request()->routeIs('admin.sertifikat.*')
                ])

               

                <li>
                    <div x-show="sidebarOpen"
                         class="px-3 pt-9 pb-0 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                        Pengaturan
                    </div>
                </li>

                @include('layouts.partials.nav-item', [
                    'href' => route('admin.users.index'),
                    'icon' => 'fa-users-gear',
                    'label' => 'Manajemen User',
                    'active' => request()->routeIs('admin.users.*')
                ])

                @include('layouts.partials.nav-item', [
                    'href' => route('admin.profil.edit'),
                    'icon' => 'fa-user-circle',
                    'label' => 'Profil',
                    'active' => request()->routeIs('admin.profil.*')
                ])

            {{-- ── MENTOR ── --}}
            @elseif($isMentor)

                <li>
                    <div x-show="sidebarOpen"
                         class="px-3 pt-1 pb-0 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                        Main
                    </div>
                </li>

                @include('layouts.partials.nav-item', [
                    'href' => route('mentor.dashboard'),
                    'icon' => 'fa-house',
                    'label' => 'Dashboard',
                    'active' => request()->routeIs('mentor.dashboard')
                ])

                <li>
                    <div x-show="sidebarOpen"
                         class="px-3 pt-9 pb-0 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                        Aktivitas
                    </div>
                </li>

                @include('layouts.partials.nav-item', [
                    'href' => route('mentor.presensi.index'),
                    'icon' => 'fa-clipboard-check',
                    'label' => 'Presensi & Tugas',
                    'active' => request()->routeIs('mentor.presensi.*')
                ])

                @include('layouts.partials.nav-item', [
                    'href' => route('mentor.rekap.index'),
                    'icon' => 'fa-table-list',
                    'label' => 'Rekap',
                    'active' => request()->routeIs('mentor.rekap.*')
                ])

                @include('layouts.partials.nav-item', [
                    'href' => route('mentor.profil.edit'),
                    'icon' => 'fa-user-circle',
                    'label' => 'Profil',
                    'active' => request()->routeIs('mentor.profil.*')
                ])

            {{-- ── PESERTA ── --}}
            @elseif($isPeserta)

                <li>
                    <div x-show="sidebarOpen"
                         class="px-3 pt-1 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                        Main
                    </div>
                </li>

                @include('layouts.partials.nav-item', [
                    'href' => route('peserta.dashboard'),
                    'icon' => 'fa-house',
                    'label' => 'Dashboard',
                    'active' => request()->routeIs('peserta.dashboard')
                ])

                <li>
                    <div x-show="sidebarOpen"
                         class="px-3 pt-3 pb-1 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                        Menu
                    </div>
                </li>

                @include('layouts.partials.nav-item', [
                    'href' => route('peserta.pelatihan.index'),
                    'icon' => 'fa-list-check',
                    'label' => 'Pelatihan',
                    'active' => request()->routeIs('peserta.pelatihan.*')
                ])

                @include('layouts.partials.nav-item', [
                    'href' => route('peserta.aktivitas.index'),
                    'icon' => 'fa-bolt',
                    'label' => 'Aktivitas Saya',
                    'active' => request()->routeIs('peserta.aktivitas.*')
                ])

                @include('layouts.partials.nav-item', [
                    'href' => route('peserta.riwayat.index'),
                    'icon' => 'fa-clock-rotate-left',
                    'label' => 'Riwayat & Sertifikat',
                    'active' => request()->routeIs('peserta.riwayat.*')
                ])

                @include('layouts.partials.nav-item', [
                    'href' => route('peserta.profil.edit'),
                    'icon' => 'fa-user-circle',
                    'label' => 'Profil',
                    'active' => request()->routeIs('peserta.profil.*')
                ])

            @endif

        </ul>
    </nav>

    {{-- Logout --}}
    <div class="p-3 border-t border-sky-100/60 bg-gradient-to-r from-sky-50/80 to-sky-50/80">
        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2 text-slate-500 hover:text-red-500 hover:bg-red-50 rounded-2xl transition-all duration-200 group">

                <i class="fa-solid fa-right-from-bracket w-5 text-center text-base shrink-0"></i>

                <span x-show="sidebarOpen"
                      class="font-medium text-sm whitespace-nowrap">
                    Logout
                </span>

            </button>
        </form>
    </div>

</div>