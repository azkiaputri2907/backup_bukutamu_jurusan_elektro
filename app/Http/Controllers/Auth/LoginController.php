<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Redirect default jika method authenticated tidak ditemukan
     */
    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Logika pengalihan setelah login berhasil
     */
    protected function authenticated(Request $request, $user)
    {
        // Cek nama role dari relasi 'role' di model User
        if ($user->role && in_array($user->role->nama_role, ['Administrator', 'Ketua Jurusan'])) {
            // Gunakan intended() agar dikembalikan ke halaman yang dituju sebelum kena redirect login
            return redirect()->intended(route('admin.dashboard')); 
        }

        // Jika tidak ada role yang cocok, kembalikan ke home public
        Auth::logout(); // Optional: Paksa logout jika role tidak valid
        return redirect()->route('guest.index')->with('error', 'Akses ditolak.');
    }

    /**
     * Logika setelah logout
     */
    protected function loggedOut(\Illuminate\Http\Request $request)
    {
        // PERBAIKAN: Diubah dari guest.landing menjadi guest.index sesuai dengan route web.php
        return redirect()->route('guest.index');
    }
}