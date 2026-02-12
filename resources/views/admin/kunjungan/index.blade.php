@extends('layouts.admin')

@section('content')

{{-- Header Section --}}
<div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold text-gray-800 tracking-tight">Data Kunjungan</h2>
        <p class="text-sm text-gray-500 font-medium">Daftar riwayat tamu yang berkunjung</p>
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
            <option value="{{ route('admin.kunjungan') }}">Semua Prodi</option>
            @php
                $prodis = ['D3 Teknik Listrik', 'D3 Teknik Elektronika', 'D3 Teknik Informatika', 'D4 Teknologi Rekayasa Pembangkit Energi', 'D4 Sistem Informasi Kota Cerdas', 'Lainnya'];
            @endphp
            @foreach($prodis as $p)
                <option value="{{ route('admin.kunjungan', ['prodi' => $p, 'search' => request('search')]) }}" 
                    {{ request('prodi') == $p ? 'selected' : '' }}>
                    {{ $p == 'Lainnya' ? 'Lainnya / Umum' : $p }}
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
                    <th class="px-6 py-4">Nomor Kunjungan</th>
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
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-xs uppercase">
                                {{ substr($row->pengunjung->nama_lengkap, 0, 1) }}
                            </div>
                            <div>
                                <span class="block font-bold text-gray-700 text-sm">{{ $row->pengunjung->nama_lengkap }}</span>
                                <span class="text-[10px] text-gray-400 font-medium">{{ $row->pengunjung->asal_instansi ?? 'Umum' }}</span>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-500">
                        <p class="line-clamp-1 max-w-xs" title="{{ $row->keperluan }}">
                            {{ Str::limit($row->keperluan, 40) }}
                        </p>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex justify-center items-center gap-2">
                            {{-- FITUR BARU: Tombol Lihat Detail --}}
                            <button @click="viewModalOpen = true" 
                                    class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 hover:scale-110 transition flex items-center justify-center"
                                    title="Lihat Detail">
                                <i class="fas fa-eye text-xs"></i>
                            </button>

                            @can('admin-only')
                            <button @click="editModalOpen = true" 
                                    class="w-8 h-8 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 hover:scale-110 transition flex items-center justify-center"
                                    title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </button>

                            <form id="delete-form-{{ $row->id }}" action="{{ route('admin.kunjungan.destroy', $row->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="button" 
                                        onclick="confirmDelete('{{ $row->id }}', '{{ $row->pengunjung->nama_lengkap }}')"
                                        class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 hover:scale-110 transition flex items-center justify-center"
                                        title="Hapus">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                            @endcan
                        </div>

                        {{-- MODAL VIEW DETAIL (Hanya data yang ada) --}}
                        <div x-show="viewModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 text-left">
                            <div @click.away="viewModalOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
                                <div class="bg-gray-50 p-4 border-b flex justify-between items-center">
                                    <h3 class="font-bold text-gray-800">Detail Kunjungan</h3>
                                    <button @click="viewModalOpen = false" class="text-gray-400 hover:text-red-500"><i class="fas fa-times"></i></button>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 uppercase block">Ref Kunjungan</label>
                                        <p class="text-sm font-bold text-purple-600">#{{ $row->nomor_kunjungan }}</p>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 uppercase block">Nama Pengunjung</label>
                                        <p class="text-sm font-bold text-gray-700">{{ $row->pengunjung->nama_lengkap }}</p>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 uppercase block">Asal Instansi / Prodi</label>
                                        <p class="text-sm text-gray-600 font-medium">{{ $row->pengunjung->asal_instansi ?? 'Umum' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 uppercase block">Keperluan</label>
                                        <div class="mt-1 bg-gray-50 p-3 rounded-xl border border-gray-100 text-sm text-gray-600 italic">
                                            "{{ $row->keperluan }}"
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4 bg-gray-50 flex justify-end">
                                    <button @click="viewModalOpen = false" class="px-4 py-2 bg-white border rounded-lg text-sm font-bold text-gray-500">Tutup</button>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL EDIT --}}
                        <div x-show="editModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 text-left">
                            <div @click.away="editModalOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
                                <div class="bg-gray-50 p-4 border-b flex justify-between items-center">
                                    <h3 class="font-bold text-gray-800">Edit Keperluan</h3>
                                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-red-500 transition"><i class="fas fa-times"></i></button>
                                </div>
                                <form action="{{ route('admin.kunjungan.update', $row->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="p-6 space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Ref Kunjungan</label>
                                            <input type="text" value="{{ $row->nomor_kunjungan }}" disabled class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm text-gray-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Keperluan</label>
                                            <textarea name="keperluan" rows="4" class="w-full border-gray-200 rounded-xl focus:ring-[#a044ff] focus:border-[#a044ff] text-sm bg-white">{{ $row->keperluan }}</textarea>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 p-4 flex justify-end gap-3">
                                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-sm font-bold text-gray-500">Batal</button>
                                        <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-[#a044ff] rounded-lg shadow-md">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        {{-- END MODALS --}}

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center">
                        <div class="text-gray-400">
                            <i class="fas fa-inbox text-2xl mb-2"></i>
                            <p class="text-sm">Belum ada data kunjungan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($kunjungan->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
        {{ $kunjungan->appends(request()->query())->links() }} 
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id, nama) {
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Data kunjungan atas nama " + nama + " akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('delete-form-' + id).submit();
        })
    }
</script>

@endsection