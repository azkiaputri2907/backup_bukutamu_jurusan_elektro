@extends('layouts.admin')

@section('content')

{{-- HEADER SECTION --}}
<div class="mb-6 md:mb-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
    <div class="flex items-center gap-3 md:gap-4">
        {{-- Animated Icon Box - Ukuran menyesuaikan mobile ke desktop --}}
        <div class="w-12 h-12 md:w-16 md:h-16 bg-gradient-to-tr from-[#ff3366] to-[#a044ff] rounded-xl md:rounded-2xl flex items-center justify-center shadow-lg shadow-purple-200 shrink-0 transform -rotate-3 hover:rotate-0 transition-transform duration-300">
            <i class="fas fa-chart-pie text-white text-xl md:text-3xl"></i>
        </div>
        
        <div class="overflow-hidden">
            <h2 class="text-xl sm:text-2xl md:text-4xl font-extrabold text-gray-800 tracking-tight leading-tight truncate">
                Dashboard <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#a044ff] to-[#3366ff]">{{ Auth::user()->role->nama_role }}</span>
            </h2>
            <div class="flex items-center gap-2 mt-1">
                <span class="w-6 md:w-8 h-1 bg-gradient-to-r from-[#ff3366] to-[#a044ff] rounded-full"></span>
                <p class="text-gray-500 font-medium tracking-wide text-[10px] md:text-sm uppercase whitespace-nowrap">Overview Statistik Hari Ini</p>
            </div>
        </div>
    </div>

    {{-- Date Display - Full width di mobile --}}
    <div class="relative w-full lg:w-auto">
        <div id="calendar-trigger" class="flex items-center justify-between lg:justify-end gap-3 bg-white px-4 py-3 rounded-2xl shadow-sm border border-gray-100 cursor-pointer hover:bg-gray-50 transition-colors duration-200">
            <div class="text-left lg:text-right">
                <p class="text-[9px] md:text-[10px] text-gray-400 font-bold uppercase tracking-widest text-left lg:text-right">Hari Ini</p>
                <p class="text-xs md:text-sm font-bold text-gray-700">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-[#a044ff] shrink-0">
                <i class="far fa-calendar-alt text-lg"></i>
            </div>
            <input type="text" id="datepicker" class="absolute inset-0 opacity-0 cursor-pointer">
        </div>
    </div>
</div>

{{-- STATS CARDS GRID --}}
{{-- Menggunakan grid-cols-2 untuk mobile agar tidak terlalu memanjang ke bawah --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-8 md:mb-10">
    
    {{-- Card 1: Total Kunjungan --}}
    <div class="bg-gradient-to-r from-[#ff3366] to-[#ff5e84] p-4 md:p-6 rounded-[1.5rem] md:rounded-[2rem] text-white shadow-lg relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
        <div class="absolute right-0 top-0 w-24 h-24 md:w-32 md:h-32 bg-white/10 rounded-full blur-2xl -mr-8 -mt-8 pointer-events-none"></div>
        <div class="relative z-10">
            <div class="bg-white/20 w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl flex items-center justify-center backdrop-blur-sm mb-3 md:mb-4">
                <i class="fas fa-users text-sm md:text-lg"></i>
            </div>
            <h3 class="text-2xl md:text-4xl font-extrabold">{{ $stats['total_tamu'] }}</h3>
            <p class="text-[10px] md:text-sm opacity-90 font-medium mt-1 uppercase lg:normal-case">Total Kunjungan</p>
        </div>
    </div>

    {{-- Card 2: Tamu Hari Ini --}}
    <div class="bg-gradient-to-r from-[#a044ff] to-[#be7dff] p-4 md:p-6 rounded-[1.5rem] md:rounded-[2rem] text-white shadow-lg relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
        <div class="absolute right-0 top-0 w-24 h-24 md:w-32 md:h-32 bg-white/10 rounded-full blur-2xl -mr-8 -mt-8 pointer-events-none"></div>
        <div class="relative z-10">
            <div class="bg-white/20 w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl flex items-center justify-center backdrop-blur-sm mb-3 md:mb-4">
                <i class="fas fa-clock text-sm md:text-lg"></i>
            </div>
            <h3 class="text-2xl md:text-4xl font-extrabold">{{ $stats['tamu_hari_ini'] }}</h3>
            <p class="text-[10px] md:text-sm opacity-90 font-medium mt-1 uppercase lg:normal-case">Tamu Hari Ini</p>
        </div>
    </div>

    {{-- Card 3: Indeks Kepuasan --}}
    <div class="bg-gradient-to-r from-[#3366ff] to-[#5e84ff] p-4 md:p-6 rounded-[1.5rem] md:rounded-[2rem] text-white shadow-lg relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
        <div class="absolute right-0 top-0 w-24 h-24 md:w-32 md:h-32 bg-white/10 rounded-full blur-2xl -mr-8 -mt-8 pointer-events-none"></div>
        <div class="relative z-10">
            <div class="bg-white/20 w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl flex items-center justify-center backdrop-blur-sm mb-3 md:mb-4">
                <i class="fas fa-star text-sm md:text-lg"></i>
            </div>
            <h3 class="text-2xl md:text-4xl font-extrabold">{{ number_format($stats['rata_rata_puas'], 1) }}</h3>
            <p class="text-[10px] md:text-sm opacity-90 font-medium mt-1 uppercase lg:normal-case">Indeks Kepuasan</p>
        </div>
    </div>

    {{-- Card 4: Data 7 Hari --}}
    <div class="bg-gradient-to-r from-[#00c6fb] to-[#005bea] p-4 md:p-6 rounded-[1.5rem] md:rounded-[2rem] text-white shadow-lg relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
        <div class="absolute right-0 top-0 w-24 h-24 md:w-32 md:h-32 bg-white/10 rounded-full blur-2xl -mr-8 -mt-8 pointer-events-none"></div>
        <div class="relative z-10">
            <div class="bg-white/20 w-8 h-8 md:w-10 md:h-10 rounded-lg md:rounded-xl flex items-center justify-center backdrop-blur-sm mb-3 md:mb-4">
                <i class="fas fa-comment-dots text-sm md:text-lg"></i>
            </div>
            <h3 class="text-2xl md:text-4xl font-extrabold">{{ $grafik->sum('jumlah') }}</h3>
            <p class="text-[10px] md:text-sm opacity-90 font-medium mt-1 uppercase lg:normal-case">Data 7 Hari</p>
        </div>
    </div>
</div>

{{-- CHARTS SECTION --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
    
    {{-- Trend Line Chart --}}
    <div class="lg:col-span-2 bg-white rounded-[1.5rem] md:rounded-[2rem] p-5 md:p-8 shadow-sm border border-gray-100 flex flex-col min-h-[400px]">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-bold text-gray-800 text-base md:text-lg">Tren Kunjungan</h3>
                <p class="text-[10px] md:text-xs text-gray-400 font-medium mt-1">Jumlah tamu seminggu terakhir</p>
            </div>
            <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-500 shrink-0">
                <i class="fas fa-chart-line text-sm md:text-base"></i>
            </div>
        </div>
        <div class="relative w-full flex-1">
            <canvas id="lineChart"></canvas>
        </div>
    </div>

    {{-- Radar Analysis Chart --}}
    <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] p-5 md:p-8 shadow-sm border border-gray-100 flex flex-col min-h-[400px]">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-bold text-gray-800 text-base md:text-lg">Analisa Kepuasan</h3>
                <p class="text-[10px] md:text-xs text-gray-400 font-medium mt-1">Skor per aspek pelayanan</p>
            </div>
            <div class="w-8 h-8 md:w-10 md:h-10 bg-pink-50 rounded-full flex items-center justify-center text-pink-500 shrink-0">
                <i class="fas fa-bullseye text-sm md:text-base"></i>
            </div>
        </div>
        <div class="relative w-full flex-1 flex justify-center items-center">
            <canvas id="radarChart"></canvas>
        </div>
    </div>
</div>

{{-- Script tetap sama dengan penyesuaian sedikit pada responsivitas Chart.js --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Flatpickr setup
        flatpickr("#datepicker", {
            locale: "id",
            dateFormat: "Y-m-d",
            defaultDate: "today",
            altInput: false, 
            monthSelectorType: "dropdown", 
            positionElement: document.getElementById('calendar-trigger'),
            position: "auto", // Biar sistem yang nentuin posisi terbaik di mobile
        });
        
        document.getElementById('calendar-trigger').addEventListener('click', function() {
            document.getElementById('datepicker')._flatpickr.open();
        });

        // Global Chart Defaults
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.color = '#94a3b8';

        // 1. Line Chart
        const ctxLine = document.getElementById('lineChart').getContext('2d');
        let gradientLine = ctxLine.createLinearGradient(0, 0, 0, 400);
        gradientLine.addColorStop(0, 'rgba(51, 102, 255, 0.2)');
        gradientLine.addColorStop(1, 'rgba(51, 102, 255, 0)');

        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: [@foreach($grafik as $g) "{{ $g->tanggal }}", @endforeach],
                datasets: [{
                    label: 'Pengunjung',
                    data: [@foreach($grafik as $g) {{ $g->jumlah }}, @endforeach],
                    borderColor: '#3366ff',
                    borderWidth: 3,
                    backgroundColor: gradientLine,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3366ff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Penting agar container div yang mengatur tinggi
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4], color: '#f1f5f9' },
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });

        // 2. Radar Chart
        const ctxRadar = document.getElementById('radarChart');
        new Chart(ctxRadar, {
            type: 'radar',
            data: {
                labels: ['Fasilitas', 'Pelayanan', 'Respon', 'Informasi', 'Kebersihan'],
                datasets: [{
                    label: 'Skor',
                    data: [
                        {{ $avg_aspek->p1 ?? 0 }}, 
                        {{ $avg_aspek->p2 ?? 0 }}, 
                        {{ $avg_aspek->p3 ?? 0 }}, 
                        {{ $avg_aspek->p4 ?? 0 }}, 
                        {{ $avg_aspek->p5 ?? 0 }}
                    ],
                    backgroundColor: 'rgba(255, 51, 102, 0.2)',
                    borderColor: '#ff3366',
                    borderWidth: 2,
                    pointBackgroundColor: '#ff3366',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: { color: '#e2e8f0' },
                        grid: { color: '#f1f5f9' },
                        pointLabels: {
                            font: { size: window.innerWidth < 768 ? 9 : 12, weight: '600' },
                            color: '#475569'
                        },
                        suggestedMin: 0,
                        suggestedMax: 5,
                        ticks: { display: false }
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
    });
</script>
@endsection