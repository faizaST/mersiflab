<header class="sticky top-0 z-40 flex flex-shrink-0 h-16 bg-white/80 backdrop-blur-xl border-b border-sky-100/70 shadow-sm shadow-sky-100/30 lg:static transition-all duration-300"
        x-data="{ userMenuOpen: false }">

    {{-- Sidebar Toggle --}}
    <button type="button"
            @click="sidebarOpen = !sidebarOpen"
            class="px-4 text-slate-500 hover:text-sky-500 focus:outline-none lg:px-6 transition-all duration-200 active:scale-95">

        <span class="sr-only">Toggle sidebar</span>

        <i class="fa-solid fa-bars text-xl"></i>
    </button>

    <div class="flex items-center justify-between flex-1 px-4 sm:px-6 lg:px-8">

        {{-- Page Title --}}
        <div class="flex-1 min-w-0">
            <h1 class="text-lg font-bold leading-7 text-slate-800 sm:truncate sm:text-xl tracking-tight">
                {{ $title ?? '' }}
            </h1>
        </div>

        {{-- Right Section --}}
        <div class="flex items-center ml-4 md:ml-6 gap-4">

            {{-- Profile Dropdown --}}
            <div class="relative ml-1">

                <button type="button"
                        @click="userMenuOpen = !userMenuOpen"
                        @click.away="userMenuOpen = false"
                        class="flex items-center gap-3 px-2 py-1.5 rounded-2xl hover:bg-slate-50 transition-all duration-200 active:scale-95"
                        id="user-menu-button"
                        aria-expanded="false"
                        aria-haspopup="true">

                    <span class="sr-only">Open user menu</span>

                    {{-- Avatar --}}
                    <img class="w-10 h-10 rounded-2xl object-cover border-2 border-sky-100 shadow-sm hover:border-sky-300 transition-all"
                         src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->name ?? 'Guest') }}&background=0EA5E9&color=fff&bold=true"
                         alt="User Avatar">

                    {{-- User Info --}}
                    <div class="hidden text-left lg:block leading-tight">
                        <p class="text-sm font-semibold text-slate-700 hover:text-sky-600 transition-colors">
                            {{ auth()->user()?->name ?? 'Guest' }}
                        </p>

                        <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">
                            {{ auth()->user()?->role ?? 'Pengunjung' }}
                        </p>
                    </div>

                    <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                </button>

                {{-- Dropdown Menu --}}
                <div x-show="userMenuOpen"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 w-56 py-2 mt-3 origin-top-right bg-white rounded-2xl shadow-xl border border-slate-100 focus:outline-none z-[999] overflow-hidden">

                    {{-- User Email --}}
                    <div class="px-4 py-3 border-b border-slate-100">
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">
                            Signed in as
                        </p>

                        <p class="text-sm font-semibold text-slate-700 truncate">
                            {{ auth()->user()?->email ?? 'guest@example.com' }}
                        </p>
                    </div>

                    {{-- Profile Menu --}}
                    <div class="py-2">

                        @php
                            $profilUrl = match(auth()->user()?->role) {
                                'admin'  => route('admin.profil.edit'),
                                'mentor' => route('mentor.profil.edit'),
                                default  => route('peserta.profil.edit'),
                            };
                        @endphp

                        <a href="{{ $profilUrl }}"
                           class="flex items-center px-4 py-2.5 text-sm text-slate-600 hover:bg-sky-50 hover:text-sky-600 transition-all">

                            <i class="fa-solid fa-user-gear mr-3 w-4 text-center text-slate-400"></i>

                            Profil Saya
                        </a>
                    </div>

                    {{-- Logout --}}
                    <form method="POST"
                          action="{{ route('logout') }}"
                          class="border-t border-slate-100 py-2">

                        @csrf

                        <button type="submit"
                                class="w-full flex items-center px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-all font-semibold">

                            <i class="fa-solid fa-right-from-bracket mr-3 w-4 text-center text-red-400"></i>

                            Logout
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</header>