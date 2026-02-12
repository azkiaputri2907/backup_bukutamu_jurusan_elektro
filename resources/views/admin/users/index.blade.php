@extends('layouts.admin')

@section('content')

{{-- Script Pendukung --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Header Section --}}
<div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6" x-data="{ addUserModal: false }">
    <div>
        <h2 class="text-2xl font-extrabold text-gray-800 tracking-tight">Pengaturan Pengguna</h2>
        <p class="text-sm text-gray-500 font-medium">Kelola akses dan akun administrator sistem.</p>
    </div>

    <div>
        <button @click="addUserModal = true" class="flex items-center gap-2 bg-gradient-to-r from-[#3366ff] to-[#a044ff] text-white px-6 py-3 rounded-xl text-sm font-bold shadow-lg shadow-blue-200 hover:scale-105 transition-transform">
            <i class="fas fa-plus-circle"></i>
            <span>Tambah User</span>
        </button>

        {{-- MODAL TAMBAH USER --}}
        <div x-show="addUserModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div @click.away="addUserModal = false" class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="bg-gray-50 p-6 border-b flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 italic">Tambah User Baru</h3>
                    <button @click="addUserModal = false" class="text-gray-400 hover:text-red-500 transition-colors"><i class="fas fa-times"></i></button>
                </div>
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="p-8 space-y-4 text-left">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nama Lengkap</label>
                            <input type="text" name="name" placeholder="Masukkan nama" class="w-full border-gray-100 bg-gray-50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm py-3 transition-all border-none" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Email</label>
                            <input type="email" name="email" placeholder="nama@poliban.ac.id" class="w-full border-gray-100 bg-gray-50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm py-3 transition-all border-none" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Password</label>
                            <input type="password" name="password" placeholder="Min. 8 karakter" class="w-full border-gray-100 bg-gray-50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm py-3 transition-all border-none" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Role</label>
                            <select name="role_id" class="w-full border-gray-100 bg-gray-50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm py-3 cursor-pointer transition-all border-none" required>
                                <option value="" disabled selected>Pilih Role...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->nama_role }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-6 flex justify-end gap-2">
                        <button type="button" @click="addUserModal = false" class="px-4 py-2 text-sm font-bold text-gray-400">Batal</button>
                        <button type="submit" class="px-8 py-3 text-sm font-bold text-white bg-blue-600 rounded-xl shadow-lg shadow-blue-100">Daftarkan User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Content Table --}}
<div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100 text-[10px] uppercase tracking-[0.15em] text-gray-400 font-black">
                    <th class="px-8 py-5">Pengguna</th>
                    <th class="px-8 py-5">Email</th>
                    <th class="px-8 py-5 text-center">Role</th>
                    <th class="px-8 py-5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50/80 transition duration-150" x-data="{ editUserModal: false, detailUserModal: false }">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="font-bold text-gray-700 text-sm">{{ $user->name }}</span>
                        </div>
                    </td>

                    <td class="px-8 py-5 text-sm text-gray-500 font-medium italic">
                        {{ $user->email }}
                    </td>

                    <td class="px-8 py-5 text-center">
                        @php
                            $color = match($user->role->nama_role ?? '') {
                                'Administrator' => 'text-red-600 bg-red-50 border-red-100',
                                'Staff' => 'text-green-600 bg-green-50 border-green-100',
                                default => 'text-gray-500 bg-gray-50 border-gray-100'
                            };
                        @endphp
                        <span class="text-[10px] font-black uppercase px-3 py-1.5 rounded-lg border {{ $color }}">
                            {{ $user->role->nama_role ?? 'No Role' }}
                        </span>
                    </td>

                    <td class="px-8 py-5">
                        <div class="flex justify-center items-center gap-3">
                            {{-- Button Lihat --}}
                            <button @click="detailUserModal = true" class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-sm" title="Detail Akun">
                                <i class="fas fa-eye text-xs"></i>
                            </button>

                            {{-- Button Edit --}}
                            <button @click="editUserModal = true" class="w-9 h-9 rounded-xl bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white transition-all flex items-center justify-center shadow-sm" title="Edit Akun">
                                <i class="fas fa-edit text-xs"></i>
                            </button>

                            {{-- Button Delete --}}
                            @if(Auth::id() !== $user->id)
                            <form id="delete-user-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDeleteUser('{{ $user->id }}', '{{ $user->name }}')"
                                        class="w-9 h-9 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm" title="Hapus Akun">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                            @else
                            <button class="w-9 h-9 rounded-xl bg-gray-100 text-gray-400 cursor-not-allowed flex items-center justify-center" title="Akun Sedang Digunakan">
                                <i class="fas fa-user-lock text-xs"></i>
                            </button>
                            @endif
                        </div>

                        {{-- MODAL DETAIL --}}
                        <div x-show="detailUserModal" style="display: none;" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 text-left">
                            <div @click.away="detailUserModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-300">
                                <div class="bg-gradient-to-br from-indigo-600 to-purple-700 p-8 text-white flex flex-col items-center text-center relative">
                                    <div class="w-20 h-20 rounded-3xl bg-white/20 backdrop-blur-md flex items-center justify-center text-3xl font-black border border-white/30 mb-4 rotate-3 shadow-xl">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <h3 class="text-xl font-extrabold">{{ $user->name }}</h3>
                                    <p class="text-indigo-200 text-[10px] font-black uppercase tracking-widest mt-1">{{ $user->role->nama_role ?? 'Staff' }}</p>
                                </div>
                                <div class="p-8 space-y-5">
                                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center"><i class="fas fa-envelope"></i></div>
                                        <div>
                                            <p class="text-[9px] font-black text-gray-400 uppercase">Email Address</p>
                                            <p class="text-sm font-bold text-gray-700">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl">
                                        <div class="w-10 h-10 rounded-xl bg-green-100 text-green-600 flex items-center justify-center"><i class="fas fa-shield-alt"></i></div>
                                        <div>
                                            <p class="text-[9px] font-black text-gray-400 uppercase">Account Status</p>
                                            <p class="text-sm font-bold text-green-600">Terverifikasi & Aktif</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-6 bg-gray-50 flex justify-center">
                                    <button @click="detailUserModal = false" class="px-8 py-3 bg-white text-gray-600 rounded-xl text-xs font-bold shadow-sm hover:bg-gray-100 transition-all border border-gray-200">Tutup Detail</button>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL EDIT --}}
                        <div x-show="editUserModal" style="display: none;" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 text-left">
                            <div @click.away="editUserModal = false" class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md overflow-hidden">
                                <div class="bg-yellow-500 p-6 text-white flex justify-between items-center">
                                    <h3 class="font-bold italic">Edit Data Pengguna</h3>
                                    <button @click="editUserModal = false" class="text-white/70 hover:text-white"><i class="fas fa-times"></i></button>
                                </div>
                                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="p-8 space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nama Lengkap</label>
                                            <input type="text" name="name" value="{{ $user->name }}" class="w-full border-none bg-gray-50 rounded-xl focus:ring-2 focus:ring-yellow-500 text-sm py-3" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Email</label>
                                            <input type="email" name="email" value="{{ $user->email }}" class="w-full border-none bg-gray-50 rounded-xl focus:ring-2 focus:ring-yellow-500 text-sm py-3" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Role</label>
                                            <select name="role_id" class="w-full border-none bg-gray-50 rounded-xl focus:ring-2 focus:ring-yellow-500 text-sm py-3 cursor-pointer">
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->nama_role }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Ganti Password <small class="lowercase font-normal text-gray-400">(Biarkan kosong jika tidak ganti)</small></label>
                                            <input type="password" name="password" class="w-full border-none bg-gray-50 rounded-xl focus:ring-2 focus:ring-yellow-500 text-sm py-3">
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 p-6 flex justify-end gap-2">
                                        <button type="button" @click="editUserModal = false" class="px-4 py-2 text-sm font-bold text-gray-400">Batal</button>
                                        <button type="submit" class="px-8 py-3 text-sm font-bold text-white bg-yellow-500 rounded-xl shadow-lg shadow-yellow-100">Simpan Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- SweetAlert Logic --}}
<script>
    function confirmDeleteUser(id, nama) {
        Swal.fire({
            title: 'Hapus User?',
            text: "Akses login " + nama + " akan ditarik. Data ini tidak bisa dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: { popup: 'rounded-[2rem]' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-user-' + id).submit();
            }
        })
    }
</script>

@endsection