<?php

    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Karyawan;
    use App\Models\Absensi;
    use App\Models\Gaji;
    use Carbon\Carbon;

    class DashboardController extends Controller
    {
        public function index()
        {
            $totalKaryawan = Karyawan::count();
            $absensiHariIni = Absensi::whereDate('tanggal_absensi', today())->count();
            $totalGajiBulanIni = Gaji::where('bulan_tahun', Carbon::now()->format('Y-m'))->sum('gaji_bersih');

            $karyawanBaru = Karyawan::orderBy('created_at', 'desc')->take(5)->get();
            $absensiTerbaru = Absensi::with('karyawan.user')->orderBy('created_at', 'desc')->take(5)->get();


            return view('admin.dashboard', compact('totalKaryawan', 'absensiHariIni', 'totalGajiBulanIni', 'karyawanBaru', 'absensiTerbaru'));
        }
    }