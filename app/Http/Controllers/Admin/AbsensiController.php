<?php

    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Absensi;
    use App\Models\Karyawan;
    use Carbon\Carbon;

    class AbsensiController extends Controller
    {
        public function index(Request $request)
        {
            $selectedMonth = $request->input('bulan', Carbon::now()->format('Y-m'));
            $selectedKaryawan = $request->input('karyawan_id');

            $karyawans = Karyawan::with('user')->orderBy('user_id')->get(); // Untuk dropdown filter

            $absensis = Absensi::with('karyawan.user')
                ->whereYear('tanggal_absensi', Carbon::parse($selectedMonth)->year)
                ->whereMonth('tanggal_absensi', Carbon::parse($selectedMonth)->month)
                ->when($selectedKaryawan, function ($query, $karyawanId) {
                    return $query->where('karyawan_id', $karyawanId);
                })
                ->orderBy('tanggal_absensi', 'desc')
                ->orderBy('karyawan_id')
                ->paginate(20);

            return view('admin.absensi.index', compact('absensis', 'karyawans', 'selectedMonth', 'selectedKaryawan'));
        }

        // FUNGSI EXPORT (Opsional, jika mau export ke Excel)
        // public function exportExcel(Request $request)
        // {
        //     // Implementasi export Excel jika dibutuhkan
        //     // Bisa menggunakan package seperti Maatwebsite/Excel
        //     // return Excel::download(new AbsensiExport($request->bulan, $request->karyawan_id), 'rekap_absensi.xlsx');
        //     return redirect()->back()->with('info', 'Fitur export belum diimplementasikan.');
        // }
    }