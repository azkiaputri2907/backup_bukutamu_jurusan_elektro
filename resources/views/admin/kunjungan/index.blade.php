@extends('layouts.admin')

@section('content')

{{-- Header Section --}}
<div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold text-gray-800 tracking-tight">Data Kunjungan</h2>
        <p class="text-sm text-gray-500 font-medium">Daftar riwayat tamu yang berkunjung (Cloud Data)</p>
    </div>

    @can('admin-only')
    <a href="{{ route('guest.form') }}" target="_blank" 
       class="group flex items-center gap-2 bg-gradient-to-r from-[#3366ff] to-[#a044ff] text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 transition transform hover:scale-105 hover:shadow-xl">
        <div class="bg-white/20 p-1 rounded-md group-hover:rotate-90 transition duration-300">
            <i class="fas fa-plus text-xs"></i>
        </div>
        <span>Tambah Manual</span>
    </a>
    @endcan
</div>

{{-- Search & Filter Section --}}
<div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row gap-3 md:gap-4">
    <form action="{{ route('admin.kunjungan') }}" method="GET" class="relative flex-1 w-full">
        @if(request('prodi'))
            <input type="hidden" name="prodi" value="{{ request('prodi') }}">
        @endif
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Pengunjung..." 
               class="w-full pl-10 pr-4 py-3 bg-gray-50 rounded-xl outline-none focus:ring-2 focus:ring-[#a044ff] focus:bg-white transition font-medium text-gray-700 text-sm">
    </form>
    
    <div class="w-full md:w-auto">
        <select onchange="window.location.href=this.value" class="w-full md:w-72 bg-gray-50 px-4 py-3 rounded-xl text-gray-600 font-bold outline-none cursor-pointer hover:bg-gray-100 transition text-sm focus:ring-2 focus:ring-[#a044ff]">
            <option value="{{ route('admin.kunjungan') }}">Semua Prodi / Instansi</option>
            @php
                $prodis = ['D3 Teknik Listrik', 'D3 Teknik Elektronika', 'D3 Teknik Informatika', 'D4 Teknologi Rekayasa Pembangkit Energi', 'D4 Sistem Informasi Kota Cerdas', 'Umum'];
            @endphp
            @foreach($prodis as $p)
                <option value="{{ route('admin.kunjungan', ['prodi' => $p, 'search' => request('search')]) }}" 
                    {{ request('prodi') == $p ? 'selected' : '' }}>
                    {{ $p }}
                </option>
            @endforeach
        </select>
    </div>
</div>

{{-- Content Card --}}
<div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-bold">
                    <th class="px-6 py-4">Nomor</th>
                    <th class="px-6 py-4">Nama Pengunjung</th>
                    <th class="px-6 py-4">Keperluan</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($kunjungan as $row)
                <tr class="hover:bg-gray-50/80 transition duration-150" x-data="{ editModalOpen: false, viewModalOpen: false }">
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-purple-50 text-[#a044ff] border border-purple-100">
                            #{{ $row->nomor_kunjungan }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs uppercase border border-indigo-100">
                                {{ substr($row->nama_lengkap, 0, 1) }}
                            </div>
                            <div>
                                <span class="block font-bold text-gray-700 text-sm">{{ $row->nama_lengkap }}</span>
                                <span class="text-[10px] text-gray-400 font-medium uppercase tracking-tighter">{{ $row->asal_instansi ?? 'Umum' }}</span>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-500">
                        <p class="line-clamp-1 max-w-xs font-medium" title="{{ $row->keperluan }}">
                            {{ Str::limit($row->keperluan, 45) }}
                        </p>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex justify-center items-center gap-2">
                            <button @click="viewModalOpen = true" class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 hover:bg-blue-600 hover:text-white transition-all shadow-sm flex items-center justify-center">
                                <i class="fas fa-eye text-xs"></i>
                            </button>

                            @can('admin-only')
                            <button @click="editModalOpen = true" class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 hover:bg-amber-600 hover:text-white transition-all shadow-sm flex items-center justify-center">
                                <i class="fas fa-edit text-xs"></i>
                            </button>

                            <form id="delete-form-{{ $row->nomor_kunjungan }}" action="{{ route('admin.kunjungan.destroy', $row->nomor_kunjungan) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete('{{ $row->nomor_kunjungan }}', '{{ $row->nama_lengkap }}')"
                                        class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 hover:bg-rose-600 hover:text-white transition-all shadow-sm flex items-center justify-center">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                            @endcan
                        </div>

                        {{-- MODAL VIEW DETAIL --}}
                        <div x-show="viewModalOpen" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4 text-left" style="display: none;" x-transition>
                            <div @click.away="viewModalOpen = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden border border-white/20">
                                <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-indigo-600 text-white flex justify-between items-center">
                                    <div>
                                        <h3 class="font-black uppercase tracking-tight text-sm">Detail Kunjungan</h3>
                                        <p class="text-[10px] text-blue-100 font-bold tracking-widest uppercase">ID: #{{ $row->nomor_kunjungan }}</p>
                                    </div>
                                    <button @click="viewModalOpen = false" class="text-white/50 hover:text-white transition"><i class="fas fa-times"></i></button>
                                </div>
                                <div class="p-8 space-y-6">
                                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                        <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg font-black">{{ substr($row->nama_lengkap, 0, 1) }}</div>
                                        <div>
                                            <h4 class="font-bold text-gray-800">{{ $row->nama_lengkap }}</h4>
                                            <p class="text-xs text-blue-600 font-bold uppercase tracking-wider">{{ $row->asal_instansi ?? 'Umum' }}</p>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Keperluan</label>
                                        <p class="text-sm text-gray-700 font-medium leading-relaxed">"{{ $row->keperluan }}"</p>
                                    </div>
                                    <button @click="viewModalOpen = false" class="w-full py-4 bg-gray-100 hover:bg-gray-200 text-gray-500 rounded-2xl font-black text-xs uppercase tracking-widest transition">Tutup Detail</button>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL EDIT (DINAMIS) --}}
                        @can('admin-only')
                        <div x-show="editModalOpen" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4 text-left" style="display: none;" x-transition>
                            <div @click.away="editModalOpen = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden border border-white/20">
                                <form action="{{ route('admin.kunjungan.update', $row->nomor_kunjungan) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="px-8 py-6 bg-gradient-to-r from-amber-500 to-orange-500 text-white flex justify-between items-center">
                                        <div>
                                            <h3 class="font-black uppercase tracking-tight text-sm">Edit Kunjungan</h3>
                                            <p class="text-[10px] text-amber-100 font-bold tracking-widest uppercase">ID: #{{ $row->nomor_kunjungan }}</p>
                                        </div>
                                        <button type="button" @click="editModalOpen = false" class="text-white/50 hover:text-white transition"><i class="fas fa-times"></i></button>
                                    </div>

                                    <div class="p-8 space-y-5">
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Pengunjung</label>
                                            <input type="text" value="{{ $row->nama_lengkap }}" disabled class="w-full bg-gray-100 border-none rounded-xl px-4 py-3 text-sm text-gray-500 font-bold cursor-not-allowed">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pilih Keperluan</label>
<select name="keperluan_master" class="w-full border-gray-100 rounded-xl focus:ring-2 focus:ring-amber-500 text-sm bg-gray-50 p-3 font-medium">
    <option value="">-- Pilih Keperluan --</option>
    
    @if(isset($keperluan_master) && count($keperluan_master) > 0)
        @foreach($keperluan_master as $km)
            <option value="{{ $km->keterangan }}" 
                {{ (isset($row) && $row->keperluan == $km->keterangan) ? 'selected' : '' }}>
                {{ $km->keterangan }}
            </option>
        @endforeach
    @endif
    
    <option value="Lainnya">Lainnya / Manual</option>
</select>                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Detail Keperluan (Opsional)</label>
                                            <textarea name="keperluan" rows="3" class="w-full border-gray-100 rounded-xl focus:ring-2 focus:ring-amber-500 text-sm bg-gray-50 p-4" placeholder="Tulis detail jika memilih 'Lainnya'...">{{ $row->keperluan }}</textarea>
                                        </div>

                                        <div class="flex gap-3 pt-2">
                                            <button type="button" @click="editModalOpen = false" class="flex-1 py-4 bg-gray-50 text-gray-400 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-100 transition">Batal</button>
                                            <button type="submit" class="flex-[2] py-4 bg-amber-500 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-amber-200 hover:bg-amber-600 transition transform hover:-translate-y-1">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endcan

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 text-gray-300 mb-4"><i class="fas fa-inbox text-2xl"></i></div>
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Tidak ada data ditemukan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-8 py-5 border-t border-gray-50 bg-gray-50/20 flex justify-between items-center">
        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Data Source: Google Sheets Cloud</span>
        <span class="text-xs font-bold text-gray-500">Total: {{ count($kunjungan) }} Baris</span>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id, nama) {
        Swal.fire({
            title: 'Hapus Data?',
            text: "Kunjungan " + nama + " akan dihapus secara permanen dari Cloud!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            border: 'none',
            borderRadius: '20px'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('delete-form-' + id).submit();
        })
    }
</script>

@endsection