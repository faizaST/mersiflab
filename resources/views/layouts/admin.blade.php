<!DOCTYPE html>
<html lang="en" class="bg-gray-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - MersifLab</title>
    
    @vite(['resources/css/app.css','resources/js/app.js'])
    
    <!-- Google Fonts: Plus Jakarta Sans for premium feel -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Phosphor Icons (Sleek modern icons) -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="flex h-screen overflow-hidden text-gray-800">

    <!-- SIDEBAR -->
    <aside class="w-[280px] bg-[#0A0F1C] text-gray-400 flex flex-col justify-between shrink-0 relative z-20">
        <!-- Brand -->
        <div class="h-20 flex items-center px-8 border-b border-white/5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-600 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-sky-500/30">
                    <i class="ph ph-rocket-launch text-xl"></i>
                </div>
                <span class="text-white text-xl font-bold tracking-tight">MersifLab</span>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Main Menu</p>
            
            <a href="/admin/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('admin/dashboard') ? 'bg-sky-600/10 text-sky-400' : 'hover:bg-white/5 hover:text-white' }}">
                <i class="ph {{ request()->is('admin/dashboard') ? 'ph-squares-four text-sky-500' : 'ph-squares-four' }} text-xl"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="/admin/pelatihan" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->is('admin/pelatihan') ? 'bg-sky-600/10 text-sky-400' : 'hover:bg-white/5 hover:text-white' }}">
                <i class="ph ph-chalkboard-teacher text-xl"></i>
                <span class="font-medium">Pelatihan</span>
            </a>

            <a href="/admin/pendaftaran" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white">
                <i class="ph ph-clipboard-text text-xl"></i>
                <span class="font-medium">Pendaftaran</span>
            </a>

            <a href="/admin/monitoring" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white">
                <i class="ph ph-chart-line-up text-xl"></i>
                <span class="font-medium">Monitoring</span>
            </a>

            <a href="/admin/sertifikat" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white">
                <i class="ph ph-certificate text-xl"></i>
                <span class="font-medium">Sertifikat & Laporan</span>
            </a>

            <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 mt-6">Settings</p>

            <a href="/admin/users" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white">
                <i class="ph ph-users-three text-xl"></i>
                <span class="font-medium">Manajemen User</span>
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col relative min-w-0">
        
        <!-- HEADER / TOPBAR -->
        <header class="h-20 glass border-b border-gray-200 px-8 flex items-center justify-between sticky top-0 z-10">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">@yield('title')</h1>
                <p class="text-sm text-gray-500 font-medium">@yield('subtitle', 'Welcome back, Admin!')</p>
            </div>

            <!-- Profile & Actions -->
            <div class="flex items-center gap-6">
                <!-- Notifications -->
                <button class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="ph ph-bell text-2xl"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                </button>

                <div class="h-8 w-px bg-gray-200"></div>

                <!-- User Profile Dropdown (Mock) -->
                <div class="flex items-center gap-3 cursor-pointer group">
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-xs font-medium text-sky-600 uppercase tracking-wider">Admin Role</p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=6366f1&color=fff&rounded=true" alt="Profile" class="w-10 h-10 rounded-full shadow-md group-hover:ring-4 ring-sky-50 transition-all">
                    
                    <form method="POST" action="{{ route('logout') }}" class="ml-2">
                        @csrf
                        <button class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-colors" title="Logout">
                            <i class="ph ph-sign-out text-xl"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <div class="flex-1 overflow-y-auto p-8 bg-[#F4F7FC]">
            @yield('content')
        </div>

    </main>

    @stack('scripts')
</body>
</html>