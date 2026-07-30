@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Dashboard Admin
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Selamat datang kembali, {{ auth()->user()->name }} 👋
            </p>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        {{-- Total Pelatihan --}}
        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-5 hover:shadow-md hover:border-emerald-200 transition-all duration-300">

            <div class="flex items-center justify-between mb-4">

                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center">
                    <i class="fa-solid fa-chalkboard-user text-emerald-600 text-lg"></i>
                </div>

            </div>

            <h3 class="text-3xl font-extrabold text-slate-800">
                {{ $total_pelatihan }}
            </h3>

            <p class="text-sm text-slate-500 mt-1">
                Total Pelatihan
            </p>

        </div>

        {{-- Peserta --}}
        <div class="bg-white rounded-2xl border border-cyan-100 shadow-sm p-5 hover:shadow-md hover:border-cyan-200 transition-all duration-300">

            <div class="flex items-center justify-between mb-4">

                <div class="w-12 h-12 rounded-2xl bg-cyan-50 flex items-center justify-center">
                    <i class="fa-solid fa-users text-cyan-600 text-lg"></i>
                </div>

            </div>

            <h3 class="text-3xl font-extrabold text-slate-800">
                {{ $total_peserta }}
            </h3>

            <p class="text-sm text-slate-500 mt-1">
                Total Peserta
            </p>

        </div>

        {{-- Mentor --}}
        <div class="bg-white rounded-2xl border border-teal-100 shadow-sm p-5 hover:shadow-md hover:border-teal-200 transition-all duration-300">

            <div class="flex items-center justify-between mb-4">

                <div class="w-12 h-12 rounded-2xl bg-teal-50 flex items-center justify-center">
                    <i class="fa-solid fa-user-tie text-teal-600 text-lg"></i>
                </div>

            </div>

            <h3 class="text-3xl font-extrabold text-slate-800">
                {{ $total_mentor }}
            </h3>

            <p class="text-sm text-slate-500 mt-1">
                Total Mentor
            </p>

        </div>

        {{-- Sertifikat --}}
        <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-5 hover:shadow-md hover:border-amber-200 transition-all duration-300">

            <div class="flex items-center justify-between mb-4">

                <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center">
                    <i class="fa-solid fa-certificate text-amber-600 text-lg"></i>
                </div>

            </div>

            <h3 class="text-3xl font-extrabold text-slate-800">
                {{ $total_sertifikat }}
            </h3>

            <p class="text-sm text-slate-500 mt-1">
                Sertifikat Diterbitkan
            </p>

        </div>

    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Statistik Pendaftar --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

            <div class="flex items-center justify-between mb-5">

                <div>
                    <h3 class="font-semibold text-slate-800">
                        Statistik Pendaftar
                    </h3>

                    <p class="text-xs text-slate-400 mt-1">
                        6 bulan terakhir
                    </p>
                </div>

                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600">
                    2026
                </span>

            </div>

            <div class="h-64">
                <canvas id="participationChart"></canvas>
            </div>

        </div>

        {{-- Kategori --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

            <div class="mb-5">

                <h3 class="font-semibold text-slate-800">
                    Kategori Pelatihan
                </h3>

                <p class="text-xs text-slate-400 mt-1">
                    Distribusi saat ini
                </p>

            </div>

            <div class="h-48 flex items-center justify-center">
                <canvas id="categoryChart"></canvas>
            </div>

            <div class="mt-5 space-y-2 max-h-32 overflow-y-auto pr-1">

                @forelse($kategori_distribution as $item)

                <div class="flex items-center justify-between text-xs">

                    <div class="flex items-center gap-2">

                        <span class="w-2.5 h-2.5 rounded-full"
                              style="background:{{ $item['color'] }}">
                        </span>

                        <span class="text-slate-600 font-medium">
                            {{ $item['nama'] }}
                        </span>

                    </div>

                    <span class="text-slate-500 font-semibold">
                        {{ $item['count'] }} Pelatihan
                    </span>

                </div>

                @empty

                <p class="text-center text-xs text-slate-400 py-3">
                    Belum ada kategori pelatihan
                </p>

                @endforelse

            </div>

        </div>

    </div>

    {{-- Pelatihan Aktif --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">

            <h3 class="font-semibold text-slate-800">
                Pelatihan Aktif
            </h3>

            <a href="{{ route('admin.pelatihan.index') }}"
               class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
                Lihat semua →
            </a>

        </div>

        @if($pelatihan_aktif->isEmpty())

        <div class="px-6 py-14 text-center">

            <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-chalkboard-user text-emerald-600 text-2xl"></i>
            </div>

            <p class="text-slate-600 font-medium">
                Belum ada pelatihan aktif
            </p>

            <a href="{{ route('admin.pelatihan.create') }}"
               class="inline-block mt-2 text-sm text-emerald-600 hover:text-emerald-700">
                Tambah pelatihan pertama
            </a>

        </div>

        @else

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead>

                    <tr class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">

                        <th class="px-6 py-4 text-left font-semibold">
                            Nama Pelatihan
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Mentor
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Kuota
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Tipe
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Status
                        </th>

                        <th class="px-6 py-4 text-left font-semibold">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    @foreach($pelatihan_aktif as $p)

                    <tr class="hover:bg-slate-50 transition-colors">

                        <td class="px-6 py-4 font-medium text-slate-800">
                            {{ $p->nama }}
                        </td>

                        <td class="px-6 py-4 text-slate-600">
                            {{ $p->mentors->pluck('name')->join(', ') ?: '—' }}
                        </td>

                        <td class="px-6 py-4 text-slate-600">
                            {{ $p->kuota }} orang
                        </td>

                        <td class="px-6 py-4">

                            @if($p->berbayar)

                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-cyan-50 text-cyan-700">
                                Berbayar
                            </span>

                            @else

                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                Gratis
                            </span>

                            @endif

                        </td>

                        <td class="px-6 py-4">

                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">

                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>

                                Aktif

                            </span>

                        </td>

                        <td class="px-6 py-4">

                            <a href="{{ route('admin.pelatihan.show', $p->id) }}"
                               class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
                                Detail →
                            </a>

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        @endif

    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = "'Outfit', sans-serif";

    //Line Chart
    const ctx = document.getElementById('participationChart').getContext('2d');

    const gradient = ctx.createLinearGradient(0, 0, 0, 260);

    gradient.addColorStop(0, 'rgba(16,185,129,0.20)');
    gradient.addColorStop(1, 'rgba(16,185,129,0)');

    new Chart(ctx, {

        type: 'line',

        data: {
            labels: ['Jan','Feb','Mar','Apr','Mei','Jun'],

            datasets: [{
                label: 'Pendaftar',
                data: {{ Js::from($chart_data) }},

                borderColor: '#10b981',
                backgroundColor: gradient,

                borderWidth: 3,
                fill: true,
                tension: 0.4,

                pointRadius: 4,
                pointHoverRadius: 6,

                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#10b981',
                pointBorderWidth: 2,
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false
                }
            },

            scales: {

                y: {
                    beginAtZero: true,

                    grid: {
                        color: 'rgba(148,163,184,0.08)'
                    },

                    border: {
                        display: false
                    }
                },

                x: {

                    grid: {
                        display: false
                    },

                    border: {
                        display: false
                    }
                }
            }
        }
    });

    // Doughnut Chart
    const ctx2 = document.getElementById('categoryChart').getContext('2d');

    const catLabels = {{ Js::from($kategori_distribution->pluck('nama')) }};
    const catData   = {{ Js::from($kategori_distribution->pluck('count')) }};
    const catColors = {{ Js::from($kategori_distribution->pluck('color')) }};

    new Chart(ctx2, {

        type: 'doughnut',

        data: {
            labels: catLabels.length > 0 ? catLabels : ['Kosong'],

            datasets: [{
                data: catData.length > 0 ? catData : [1],
                backgroundColor: catColors.length > 0 ? catColors : ['#cbd5e1'],
                borderWidth: 0,
                hoverOffset: 5
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false
                }
            },

            cutout: '72%'
        }
    });

});
</script>
@endpush