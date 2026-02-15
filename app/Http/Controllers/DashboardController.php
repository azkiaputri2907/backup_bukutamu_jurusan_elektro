<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // User tetap di Database Lokal demi keamanan
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Fungsi pembantu untuk mengambil semua data dari Google Sheets
    private function fetchCloudData()
    {
        try {
            $response = Http::timeout(15)->get(env('GOOGLE_SCRIPT_URL'), [
                'action' => 'getAllData'
            ]);
            if ($response->successful()) {
                return $response->json()['data'];
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Cloud Error: ' . $e->getMessage());
            return null;
        }
    }

    public function index()
    {
        $stats = ['total_tamu' => 0, 'tamu_hari_ini' => 0, 'rata_rata_puas' => 0];
        $grafik = collect([]);
        $avg_aspek = (object)['p1'=>0, 'p2'=>0, 'p3'=>0, 'p4'=>0, 'p5'=>0];

        try {
            $response = Http::get(env('GOOGLE_SCRIPT_URL'), ['action' => 'getDashboardData']);
            if ($response->successful()) {
                $d = $response->json()['data'];
                $stats = [
                    'total_tamu' => $d['totalKunjungan'],
                    'tamu_hari_ini' => $d['kunjunganHariIni'],
                    'rata_rata_puas' => $d['rataRataSurvey'],
                ];
                $grafik = collect($d['grafikMingguan'])->map(fn($i) => (object)$i);
                $avg_aspek = (object)$d['rataAspek'];
            }
        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());
        }

        return view('admin.dashboard', compact('stats', 'grafik', 'avg_aspek'));
    }

    // --- DATA KUNJUNGAN ---
public function kunjungan(Request $request)
{
    $kunjungan = collect([]);

    try {
        $response = Http::get(env('GOOGLE_SCRIPT_URL'), [
            'action' => 'getAllData'
        ]);

        if ($response->successful()) {
            $data = $response->json()['data'] ?? [];
            $raw  = $data['bukutamu'] ?? [];

            if (count($raw) > 1) {
                array_shift($raw); // hapus header
            }

            $kunjungan = collect($raw)->map(fn($row) => (object)[
                'nomor_kunjungan' => $row[0] ?? '-',
                'tanggal'         => $row[1] ?? '-',
                'hari'            => $row[2] ?? '-',
                'nama_lengkap'    => $row[3] ?? '-',
                'asal_instansi'   => $row[4] ?? '-',
                'keperluan'       => $row[5] ?? '-',
                'detail_keperluan'=> $row[6] ?? '-',
            ])->reverse();
        }

    } catch (\Exception $e) {
        Log::error('Kunjungan Error: ' . $e->getMessage());
    }

    // filter search
    if ($request->search) {
        $kunjungan = $kunjungan->filter(fn($item) =>
            str_contains(strtolower($item->nama_lengkap), strtolower($request->search)) ||
            str_contains(strtolower($item->nomor_kunjungan), strtolower($request->search))
        );
    }

    // filter prodi
    if ($request->prodi) {
        $kunjungan = $kunjungan->where('asal_instansi', $request->prodi);
    }

    return view('admin.kunjungan.index', compact('kunjungan'));
}


    // --- DATA SURVEY ---
    public function survey(Request $request)
    {
        $data = $this->fetchCloudData();
        $raw = $data['survey'] ?? [];
        if (count($raw) > 1) array_shift($raw);

        $surveys = collect($raw)->map(fn($row) => (object)[
            'waktu'        => $row[0],
            'id_kunjungan' => $row[1],
            'nama_tamu'    => $row[2],
            'p1' => $row[3], 'p2' => $row[4], 'p3' => $row[5], 'p4' => $row[6], 'p5' => $row[7],
            'kritik_saran' => $row[8] ?? '-',
            'rata_rata'    => number_format(($row[3]+$row[4]+$row[5]+$row[6]+$row[7])/5, 1)
        ]);

        $avgScores = [
            $surveys->avg('p1') ?: 0,
            $surveys->avg('p2') ?: 0,
            $surveys->avg('p3') ?: 0,
            $surveys->avg('p4') ?: 0,
            $surveys->avg('p5') ?: 0,
        ];

        return view('admin.survey.index', compact('surveys', 'avgScores'));
    }

    // --- DATA PENGUNJUNG ---
    public function pengunjung(Request $request)
    {
        $data = $this->fetchCloudData();
        $raw = $data['pengunjung'] ?? [];
        if (count($raw) > 1) array_shift($raw);

        $pengunjung = collect($raw)->map(fn($row) => (object)[
            'identitas_no'  => $row[0],
            'nama_lengkap'  => $row[1],
            'asal_instansi' => $row[2],
            'terakhir_kunjungan' => $row[3] ?? '-',
        ]);

        if ($request->search) {
            $pengunjung = $pengunjung->filter(fn($p) => 
                str_contains(strtolower($p->nama_lengkap), strtolower($request->search)) ||
                str_contains($p->identitas_no, $request->search)
            );
        }

        return view('admin.pengunjung.index', compact('pengunjung'));
    }

    // --- DATA USER (Tetap di Local Database untuk Keamanan Login) ---
    public function users()
    {
        $users = User::all();
        $roles = DB::table('master_role')->get();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $request->validate(['name' => 'required', 'email' => 'required|unique:users', 'password' => 'required']);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id ?? 2
        ]);
        return back()->with('success', 'User berhasil ditambahkan.');
    }

    // --- MASTER KEPERLUAN ---
    public function masterKeperluan()
    {
        $data = $this->fetchCloudData();
        $raw = $data['master_keperluan'] ?? [];
        if (count($raw) > 0) array_shift($raw);

        $keperluan = collect($raw)->map(fn($row) => (object)[
            'id' => $row[0] ?? 0,
            'keterangan' => $row[1] ?? '-'
        ]);

        return view('admin.master.keperluan', compact('keperluan'));
    }

    // --- LAPORAN ---
    public function laporan()
    {
        // Ambil prodi dari database lokal (jika masih ada) atau return array statis
        $prodi = DB::table('master_prodi_instansi')->get(); 
        return view('admin.laporan.index', compact('prodi'));
    }

    public function exportLaporan(Request $request)
    {
        // Untuk laporan, kita arahkan admin ke Google Sheets langsung
        // Karena data sudah ada di sana, lebih akurat jika admin melihat/unduh dari sana
        $scriptUrl = env('GOOGLE_SCRIPT_URL');
        return redirect()->away($scriptUrl . "?action=getDashboardData"); 
        // Atau kamu bisa arahkan ke URL spreadsheet langsung
    }

    public function updateKunjungan(Request $request, $id)
{
    try {
        $response = Http::post(env('GOOGLE_SCRIPT_URL'), [
            'action'    => 'updateKunjungan',
            'id'        => $id,
            'keperluan' => $request->keperluan,
        ]);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Data berhasil diupdate.');
        }

        return redirect()->back()->with('error', 'Gagal update data.');
    } catch (\Exception $e) {
        Log::error('Update Kunjungan Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan saat update.');
    }
}

public function destroyKunjungan($id)
{
    try {
        $response = Http::post(env('GOOGLE_SCRIPT_URL'), [
            'action' => 'deleteKunjungan',
            'id'     => $id,
        ]);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Gagal menghapus data.');
    } catch (\Exception $e) {
        Log::error('Delete Kunjungan Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan saat hapus.');
    }
}


}