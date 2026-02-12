@extends('layouts.admin')

@section('content')

{{-- Script SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Header Section --}}
<div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-gray-800 tracking-tight">Data Survey Kepuasan</h2>
        <p class="text-sm text-gray-500 font-medium">Hasil penilaian pengunjung terhadap layanan kami.</p>
    </div>
    
    <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-2xl shadow-sm border border-gray-100">
        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
            <i class="fas fa-star"></i>
        </div>
        <div class="flex flex-col">
            <span class="text-[10px] uppercase font-bold text-gray-400 leading-none">Skor Maksimal</span>
            <span class="text-sm font-black text-gray-700">5.00 Point</span>
        </div>
    </div>
</div>

<div class="flex flex-col lg:flex-row gap-8">

    {{-- === KOLOM KIRI: DAFTAR SURVEY === --}}
    <div class="flex-1 w-full lg:w-2/3">

{{-- Search & Filter Section --}}
<div class="bg-white p-4 rounded-[1.5rem] shadow-sm border border-gray-100 mb-6">
    <form action="{{ route('admin.survey') }}" method="GET" class="flex flex-col xl:flex-row gap-4">
        {{-- Search Input --}}
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pengunjung..." 
                   class="w-full pl-11 pr-4 py-3 bg-gray-50 rounded-xl border-none outline-none focus:ring-2 focus:ring-[#a044ff]/20 focus:bg-white transition font-medium text-sm text-gray-700">
        </div>
        
        {{-- Filter Prodi --}}
        <select name="prodi" onchange="this.form.submit()" class="w-full xl:w-64 bg-gray-50 px-4 py-3 rounded-xl border-none text-gray-600 font-bold outline-none cursor-pointer hover:bg-gray-100 transition text-sm focus:ring-2 focus:ring-[#a044ff]/20">
            <option value="">Semua Prodi / Instansi</option>
            @foreach($prodis as $p)
                <option value="{{ $p }}" {{ request('prodi') == $p ? 'selected' : '' }}>{{ $p }}</option>
            @endforeach
        </select>
        
        {{-- Filter Rating --}}
        <select name="rating" onchange="this.form.submit()" class="w-full xl:w-56 bg-gray-50 px-4 py-3 rounded-xl border-none text-gray-600 font-bold outline-none cursor-pointer hover:bg-gray-100 transition text-sm focus:ring-2 focus:ring-[#a044ff]/20">
            <option value="">Semua Rating</option>
            <option value="low" {{ request('rating') == 'low' ? 'selected' : '' }}>⭐ Rating Rendah (< 3)</option>
            <option value="high" {{ request('rating') == 'high' ? 'selected' : '' }}>⭐ Rating Tinggi (>= 4)</option>
        </select>

        {{-- Reset Button (Opsional) --}}
        @if(request()->anyFilled(['search', 'prodi', 'rating']))
            <a href="{{ route('admin.survey') }}" class="flex items-center justify-center px-4 py-3 bg-rose-50 text-rose-500 rounded-xl hover:bg-rose-100 transition text-sm font-bold">
                <i class="fas fa-sync-alt"></i>
            </a>
        @endif
    </form>
</div>

        {{-- Content Table --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-6 py-5 text-[11px] uppercase tracking-widest text-gray-400 font-black">Tanggal</th>
                            <th class="px-6 py-5 text-[11px] uppercase tracking-widest text-gray-400 font-black">Pengunjung</th>
                            <th class="px-6 py-5 text-[11px] uppercase tracking-widest text-gray-400 font-black text-center">Detail Skor</th>
                            <th class="px-6 py-5 text-[11px] uppercase tracking-widest text-gray-400 font-black text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($surveys as $s)
                        <tr class="hover:bg-gray-50/50 transition duration-150" x-data="{ editModalOpen: false, viewModalOpen: false }">
                            
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-700">{{ \Carbon\Carbon::parse($s->created_at)->format('d M Y') }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-purple-500 to-[#a044ff] flex items-center justify-center text-white font-bold text-sm shadow-sm uppercase">
                                        {{ substr($s->kunjungan->pengunjung->nama_lengkap ?? 'A', 0, 1) }}
                                    </div>
                                    <span class="font-bold text-gray-700 text-sm tracking-tight">
                                        {{ $s->kunjungan->pengunjung->nama_lengkap ?? 'Anonim' }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-1.5">
                                    @foreach(['p1', 'p2', 'p3', 'p4', 'p5'] as $p)
                                        @php $val = $s->detail->$p ?? 0; @endphp
                                        <div class="w-6 h-6 flex items-center justify-center rounded-lg text-[10px] font-black shadow-sm
                                            {{ $val >= 4 ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : ($val >= 3 ? 'bg-amber-50 text-amber-600 border border-amber-100' : 'bg-rose-50 text-rose-600 border border-rose-100') }}">
                                            {{ $val }}
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex justify-center items-center gap-2">
                                    <button @click="viewModalOpen = true" title="Lihat Detail" class="w-9 h-9 rounded-xl bg-white border border-gray-100 text-blue-500 hover:bg-blue-500 hover:text-white transition-all duration-200 shadow-sm flex items-center justify-center">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>

                                    @can('admin-only')
                                    <button @click="editModalOpen = true" title="Edit Data" class="w-9 h-9 rounded-xl bg-white border border-gray-100 text-amber-500 hover:bg-amber-500 hover:text-white transition-all duration-200 shadow-sm flex items-center justify-center">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>

                                    <form id="delete-form-{{ $s->id }}" action="{{ route('admin.survey.destroy', $s->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete('{{ $s->id }}', '{{ $s->kunjungan->pengunjung->nama_lengkap ?? 'Anonim' }}')"
                                                class="w-9 h-9 rounded-xl bg-white border border-gray-100 text-rose-500 hover:bg-rose-500 hover:text-white transition-all duration-200 shadow-sm flex items-center justify-center">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>

                                {{-- MODAL VIEW DETAIL --}}
                                <div x-show="viewModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4 text-left">
                                    <div @click.away="viewModalOpen = false" class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden">
                                        <div class="px-6 py-5 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                                            <h3 class="font-black text-gray-800 text-base uppercase tracking-tight">Detail Survey</h3>
                                            <button @click="viewModalOpen = false" class="w-8 h-8 flex items-center justify-center rounded-full bg-white text-gray-400 hover:text-rose-500 transition shadow-sm"><i class="fas fa-times text-xs"></i></button>
                                        </div>
                                        <div class="p-8 space-y-6">
                                            <div class="flex items-center gap-4 p-4 bg-purple-50 rounded-2xl border border-purple-100">
                                                <div class="w-12 h-12 rounded-xl bg-[#a044ff] text-white flex items-center justify-center text-lg font-bold">
                                                    {{ substr($s->kunjungan->pengunjung->nama_lengkap ?? 'A', 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-[10px] font-bold text-purple-400 uppercase tracking-widest leading-none">Pengunjung</p>
                                                    <p class="text-base font-black text-gray-800">{{ $s->kunjungan->pengunjung->nama_lengkap ?? 'Anonim' }}</p>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-5 gap-2">
                                                @foreach(['Kecepatan', 'Etika', 'Kompetensi', 'Fasilitas', 'Kualitas'] as $idx => $label)
                                                    @php $p_key = 'p'.($idx+1); $val = $s->detail->$p_key ?? 0; @endphp
                                                    <div class="text-center group">
                                                        <div class="w-full aspect-square flex items-center justify-center rounded-xl border border-gray-100 bg-gray-50 font-black text-gray-700 text-sm group-hover:border-purple-200 transition">
                                                            {{ $val }}
                                                        </div>
                                                        <span class="text-[8px] text-gray-400 block mt-1.5 font-bold uppercase tracking-tighter">{{ $label }}</span>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="space-y-2">
                                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kritik & Saran</label>
                                                <div class="bg-gray-50 p-4 rounded-2xl text-sm text-gray-600 leading-relaxed italic border border-gray-100 min-h-[80px]">
                                                    "{{ $s->saran ?? ($s->kritik_saran ?? 'Tidak ada saran yang diberikan.') }}"
                                                </div>
                                            </div>
                                        </div>
                                        <div class="p-6 bg-gray-50/50 text-right">
                                            <button @click="viewModalOpen = false" class="px-8 py-3 bg-white border border-gray-200 rounded-xl font-bold text-gray-500 text-xs shadow-sm hover:bg-gray-100 transition">Tutup Detail</button>
                                        </div>
                                    </div>
                                </div>

                                {{-- MODAL EDIT --}}
                                @can('admin-only')
                                <div x-show="editModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4 text-left">
                                    <div @click.away="editModalOpen = false" class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden">
                                        <div class="px-6 py-5 border-b border-gray-50 font-black text-gray-800 uppercase tracking-tight">Update Data Survey</div>
                                        <form action="{{ route('admin.survey.update', $s->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="p-8 space-y-6">
                                                <div class="grid grid-cols-5 gap-3">
                                                    @foreach(['p1', 'p2', 'p3', 'p4', 'p5'] as $idx => $key)
                                                    <div>
                                                        <span class="text-[10px] text-gray-400 block text-center font-bold mb-2 uppercase">P{{$loop->iteration}}</span>
                                                        <input type="number" name="{{ $key }}" min="1" max="5" value="{{ $s->detail->$key ?? 0 }}" 
                                                               class="w-full text-center border-gray-200 bg-gray-50 rounded-xl text-sm font-bold focus:ring-[#a044ff] focus:bg-white transition">
                                                    </div>
                                                    @endforeach
                                                </div>
                                                <div class="space-y-2">
                                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Kritik & Saran</label>
                                                    <textarea name="saran" rows="4" class="w-full border-gray-200 bg-gray-50 rounded-2xl focus:ring-[#a044ff] focus:bg-white transition text-sm p-4">{{ $s->saran ?? $s->kritik_saran }}</textarea>
                                                </div>
                                            </div>
                                            <div class="p-6 bg-gray-50/50 flex justify-end gap-3">
                                                <button type="button" @click="editModalOpen = false" class="px-6 py-3 text-xs font-bold text-gray-400">Batal</button>
                                                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-[#a044ff] text-white rounded-xl text-xs font-black shadow-lg shadow-purple-200 hover:scale-105 transition-transform">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endcan

                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400 font-medium italic text-sm">Tidak ada data yang ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($surveys->hasPages())
            <div class="px-6 py-5 border-t border-gray-50 bg-gray-50/20">
                {{ $surveys->appends(request()->query())->links() }} 
            </div>
            @endif
        </div>
    </div>

    {{-- === KOLOM KANAN: STATISTIK === --}}
    <div class="w-full lg:w-1/3">
        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 sticky top-8">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-[#a044ff] flex items-center justify-center shadow-sm">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="text-base font-black text-gray-800 uppercase tracking-tight">Analisis Skor</h3>
            </div>
            
            <div class="space-y-6">
                @php $aspekLabels = ['Kecepatan', 'Etika', 'Kompetensi', 'Fasilitas', 'Kualitas']; @endphp
                @foreach($avgScores as $index => $score)
                <div class="group">
                    <div class="flex justify-between items-center mb-2.5">
                        <span class="text-[11px] font-black text-gray-400 uppercase tracking-widest group-hover:text-purple-500 transition">{{ $aspekLabels[$index] ?? 'Aspek '.($index+1) }}</span>
                        <div class="bg-purple-50 px-2 py-1 rounded-md">
                            <span class="text-xs font-black text-[#a044ff]">{{ number_format($score, 1) }}</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-50 rounded-full h-2 overflow-hidden border border-gray-100">
                        <div class="bg-gradient-to-r from-indigo-500 to-[#a044ff] h-full rounded-full transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(160,68,255,0.3)]" 
                             style="width: {{ ($score/5)*100 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-10 p-5 bg-gradient-to-br from-gray-50 to-white rounded-2xl border border-gray-100">
                <p class="text-[10px] text-gray-400 font-bold uppercase mb-2 tracking-widest leading-none">Insight</p>
                <p class="text-xs text-gray-500 leading-relaxed italic">"Rata-rata kepuasan dihitung secara otomatis berdasarkan total survei yang masuk hari ini."</p>
            </div>
        </div>
    </div>

</div>

<script>
    function confirmDelete(id, nama) {
        Swal.fire({
            title: 'Hapus data ini?',
            text: "Survey milik " + nama + " akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f43f5e', 
            confirmButtonText: 'Ya, Hapus Data',
            cancelButtonText: 'Kembali',
            reverseButtons: true,
            background: '#ffffff',
            customClass: {
                popup: 'rounded-[1.5rem]',
                confirmButton: 'rounded-xl font-bold px-6 py-3',
                cancelButton: 'rounded-xl font-bold px-6 py-3'
            }
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('delete-form-' + id).submit();
        })
    }
</script>

@endsection