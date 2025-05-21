<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Absensi;
use App\Models\Karyawan;
use Carbon\Carbon;

class KaryawanController extends Controller
{
    public function dashboard()
    {
        $karyawan = Auth::user()->karyawan; // Dapatkan data karyawan dari user yang login
        $absensiHariIni = $karyawan->absensiHariIni();
        $sudahAbsenMasuk = $karyawan->sudahAbsenMasukHariIni();
        $sudahAbsenPulang = $karyawan->sudahAbsenPulangHariIni();

        return view('karyawan.dashboard', compact('karyawan', 'absensiHariIni', 'sudahAbsenMasuk', 'sudahAbsenPulang'));
    }

    public function presensiMasuk(Request $request)
    {
        $karyawan = Auth::user()->karyawan;

        if ($karyawan->sudahAbsenMasukHariIni()) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Anda sudah melakukan presensi masuk hari ini.');
        }

        Absensi::create([
            'karyawan_id' => $karyawan->id,
            'tanggal_absensi' => today(),
            'jam_masuk' => now(),
            'status_kehadiran' => 'Hadir (Belum Pulang)',
        ]);

        return redirect()->route('karyawan.dashboard')->with('success', 'Presensi masuk berhasil dicatat.');
    }

    public function presensiPulang(Request $request)
    {
        $karyawan = Auth::user()->karyawan;
        $absensiHariIni = $karyawan->absensiHariIni();

        if (!$absensiHariIni || !$absensiHariIni->jam_masuk) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Anda belum melakukan presensi masuk hari ini.');
        }

        if ($karyawan->sudahAbsenPulangHariIni()) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Anda sudah melakukan presensi pulang hari ini.');
        }

        $absensiHariIni->update([
            'jam_pulang' => now(),
            'status_kehadiran' => 'Hadir',
        ]);

        return redirect()->route('karyawan.dashboard')->with('success', 'Presensi pulang berhasil dicatat.');
    }

    public function riwayatAbsensi()
    {
        $karyawan = Auth::user()->karyawan;
        $riwayatAbsensi = Absensi::where('karyawan_id', $karyawan->id)
                                  ->orderBy('tanggal_absensi', 'desc')
                                  ->paginate(10); // Paginasi

        return view('karyawan.riwayat_absensi', compact('karyawan', 'riwayatAbsensi'));
    }
}