@extends('layouts.admin')

@section('content')

{{-- Script Pendukung --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Header Section --}}
<div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6" x-data="{ addModalOpen: false }">
    <div>
        <h2 class="text-2xl font-extrabold text-gray-800 tracking-tight">Master Keperluan</h2>
        <p class="text-sm text-gray-500 font-medium">Manajemen daftar pilihan keperluan kunjungan tamu.</p>
    </div>

    {{-- Tombol Tambah --}}
    <button @click="addModalOpen = true" 
        class="group flex items-center gap-2 bg-gradient-to-r from-[#3366ff] to-[#a044ff] text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 transition transform hover:scale-105 hover:shadow-xl">
        <div class="bg-white/20 p-1 rounded-md group-hover:rotate-90 transition duration-300">
            <i class="fas fa-plus text-xs"></i>
        </div>
        <span>Tambah Keperluan</span>
    </button>

    {{-- MODAL TAMBAH --}}
    <div x-show="addModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div @click.away="addModalOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-gray-50 p-4 border-b flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Tambah Data Baru</h3>
                <button @click="addModalOpen = false" class="text-gray-400 hover:text-red-500"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.keperluan.store') }}" method="POST">
                @csrf
                <div class="p-6">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Keterangan Keperluan</label>
                    <textarea name="keterangan" rows="3" required placeholder="Contoh: Koordinasi Kurikulum"
                        class="w-full border-gray-200 rounded-xl focus:ring-[#a044ff] focus:border-[#a044ff] text-sm bg-gray-50 focus:bg-white transition"></textarea>
                </div>
                <div class="bg-gray-50 p-4 flex justify-end gap-2">
                    <button type="button" @click="addModalOpen = false" class="px-4 py-2 text-sm font-bold text-gray-500">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-gradient-to-r from-[#3366ff] to-[#a044ff] rounded-lg shadow-md">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Content Card --}}
<div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-bold">
                    <th class="px-6 py-4 w-20">No</th>
                    <th class="px-6 py-4">Keterangan Keperluan</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($keperluan as $k)
                <tr class="hover:bg-gray-50/80 transition duration-150" x-data="{ editModalOpen: false, showModalOpen: false }">
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-gray-400">#{{ $loop->iteration }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-gray-700 truncate block max-w-xs">{{ $k->keterangan }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-center items-center gap-2">
                            {{-- Tombol Lihat --}}
                            <button @click="showModalOpen = true" 
                                class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 hover:scale-110 transition flex items-center justify-center shadow-sm">
                                <i class="fas fa-eye text-xs"></i>
                            </button>

                            {{-- Tombol Edit --}}
                            <button @click="editModalOpen = true" 
                                class="w-8 h-8 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 hover:scale-110 transition flex items-center justify-center shadow-sm">
                                <i class="fas fa-edit text-xs"></i>
                            </button>

                            {{-- Form Hapus --}}
                            <form id="delete-form-{{ $k->id }}" action="{{ route('admin.keperluan.destroy', $k->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete('{{ $k->id }}', '{{ $k->keterangan }}')"
                                    class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 hover:scale-110 transition flex items-center justify-center shadow-sm">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>

                        {{-- MODAL DETAIL (Lihat) --}}
                        <div x-show="showModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                            <div @click.away="showModalOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
                                <div class="bg-blue-600 p-4 flex justify-between items-center text-white">
                                    <h3 class="font-bold flex items-center gap-2"><i class="fas fa-info-circle"></i> Detail Keperluan</h3>
                                    <button @click="showModalOpen = false" class="text-white/80 hover:text-white"><i class="fas fa-times"></i></button>
                                </div>
                                <div class="p-8">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Isi Keterangan:</p>
                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                        <p class="text-gray-700 leading-relaxed font-medium">{{ $k->keterangan }}</p>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                                    <button @click="showModalOpen = false" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-300 transition">Tutup</button>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL EDIT --}}
                        <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                            <div @click.away="editModalOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
                                <div class="bg-gray-50 p-4 border-b flex justify-between items-center">
                                    <h3 class="font-bold text-gray-800">Edit Keperluan</h3>
                                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-red-500"><i class="fas fa-times"></i></button>
                                </div>
                                <form action="{{ route('admin.keperluan.update', $k->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="p-6">
                                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Keterangan Keperluan</label>
                                        <textarea name="keterangan" rows="3" required
                                            class="w-full border-gray-200 rounded-xl focus:ring-[#a044ff] focus:border-[#a044ff] text-sm bg-gray-50 focus:bg-white transition">{{ $k->keterangan }}</textarea>
                                    </div>
                                    <div class="bg-gray-50 p-4 flex justify-end gap-2">
                                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-sm font-bold text-gray-500">Batal</button>
                                        <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-gradient-to-r from-yellow-400 to-orange-500 rounded-lg shadow-md">Update Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-10 text-center text-gray-400">Belum ada data keperluan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function confirmDelete(id, text) {
        Swal.fire({
            title: 'Hapus Data?',
            text: "Keperluan '" + text + "' akan dihapus selamanya!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>

@endsection