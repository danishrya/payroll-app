<?php

    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Gaji;
    use App\Models\Karyawan;
    use App\Models\Absensi;
    use Carbon\Carbon;
    use Carbon\CarbonPeriod; // Untuk menghitung hari kerja
    use Illuminate\Support\Facades\DB;

    class GajiController extends Controller
    {
        public function index(Request $request)
        {
            $selectedMonth = $request->input('bulan_tahun', Carbon::now()->format('Y-m'));
            $selectedKaryawan = $request->input('karyawan_id');
            $statusPembayaran = $request->input('status_pembayaran');

            $karyawans = Karyawan::with('user')->orderBy('user_id')->get(); // Untuk dropdown filter

            $gajis = Gaji::with('karyawan.user')
                ->where('bulan_tahun', $selectedMonth)
                ->when($selectedKaryawan, function ($query, $karyawanId) {
                    return $query->where('karyawan_id', $karyawanId);
                })
                ->when($statusPembayaran, function ($query, $status) {
                    return $query->where('status_pembayaran', $status);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('admin.gaji.index', compact('gajis', 'karyawans', 'selectedMonth', 'selectedKaryawan', 'statusPembayaran'));
        }

        public function showHitungForm()
        {
            $karyawans = Karyawan::with('user')->orderBy('user_id')->get();
            return view('admin.gaji.hitung', compact('karyawans'));
        }

        public function hitungDanSimpanGaji(Request $request)
        {
            $request->validate([
                'bulan_tahun' => 'required|date_format:Y-m',
                'karyawan_ids' => 'nullable|array', // Bisa pilih semua atau beberapa
                'karyawan_ids.*' => 'exists:karyawans,id',
                'potongan_per_hari' => 'required|numeric|min:0',
                'hari_libur_nasional' => 'nullable|string', // format: Y-m-d,Y-m-d
            ]);

            $bulanTahun = $request->bulan_tahun;
            $targetKaryawanIds = $request->karyawan_ids;
            $potonganPerHari = $request->potongan_per_hari;
            $parsedBulanTahun = Carbon::createFromFormat('Y-m', $bulanTahun);

            // Tentukan karyawan yang akan diproses
            if (empty($targetKaryawanIds) || in_array('all', $targetKaryawanIds)) {
                $karyawansToProcess = Karyawan::all();
            } else {
                $karyawansToProcess = Karyawan::whereIn('id', $targetKaryawanIds)->get();
            }

            if ($karyawansToProcess->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada karyawan yang dipilih atau ditemukan.');
            }

            // Daftar hari libur nasional (jika ada)
            $liburNasional = [];
            if ($request->filled('hari_libur_nasional')) {
                $liburNasional = explode(',', $request->hari_libur_nasional);
                $liburNasional = array_map('trim', $liburNasional);
            }

            // Hitung jumlah hari kerja dalam sebulan (Senin-Jumat, dikurangi libur nasional)
            $awalBulan = $parsedBulanTahun->copy()->startOfMonth();
            $akhirBulan = $parsedBulanTahun->copy()->endOfMonth();
            $period = CarbonPeriod::create($awalBulan, $akhirBulan);
            $jumlahHariKerjaSebulan = 0;
            foreach ($period as $date) {
                if ($date->isWeekday() && !in_array($date->format('Y-m-d'), $liburNasional)) {
                    $jumlahHariKerjaSebulan++;
                }
            }
            if($jumlahHariKerjaSebulan == 0) {
                 return redirect()->back()->with('error', 'Jumlah hari kerja pada bulan terpilih adalah 0. Periksa input hari libur.');
            }


            DB::beginTransaction();
            try {
                $gajiGeneratedCount = 0;
                foreach ($karyawansToProcess as $karyawan) {
                    // Cek apakah gaji untuk karyawan dan bulan ini sudah ada
                    $existingGaji = Gaji::where('karyawan_id', $karyawan->id)
                                        ->where('bulan_tahun', $bulanTahun)
                                        ->first();
                    if ($existingGaji && $request->input('timpa_data') !== 'yes') {
                        // Lewati jika sudah ada dan tidak diminta timpa
                        continue;
                    }

                    $jumlahKehadiran = Absensi::where('karyawan_id', $karyawan->id)
                        ->whereYear('tanggal_absensi', $parsedBulanTahun->year)
                        ->whereMonth('tanggal_absensi', $parsedBulanTahun->month)
                        ->where(function ($query) { // Hanya hitung yang 'Hadir' atau sudah 'Pulang'
                            $query->where('status_kehadiran', 'Hadir')
                                  ->orWhereNotNull('jam_pulang');
                        })
                        ->count();
                    
                    // Asumsi: ketidakhadiran adalah selisih hari kerja dengan kehadiran
                    // Ini bisa disesuaikan jika ada status Izin/Sakit yang tidak dihitung sbg potongan
                    $jumlahKetidakhadiran = max(0, $jumlahHariKerjaSebulan - $jumlahKehadiran);
                    $potonganTotal = $jumlahKetidakhadiran * $potonganPerHari;
                    $gajiBersih = $karyawan->gaji_pokok - $potonganTotal;

                    $dataGaji = [
                        'karyawan_id' => $karyawan->id,
                        'bulan_tahun' => $bulanTahun,
                        'gaji_pokok' => $karyawan->gaji_pokok,
                        'jumlah_hari_kerja' => $jumlahHariKerjaSebulan,
                        'jumlah_kehadiran' => $jumlahKehadiran,
                        'jumlah_ketidakhadiran' => $jumlahKetidakhadiran,
                        'potongan_ketidakhadiran' => $potonganTotal,
                        'gaji_bersih' => max(0, $gajiBersih), // Gaji bersih tidak boleh minus
                        'status_pembayaran' => 'Belum Dibayar', // Default
                    ];

                    if($existingGaji) {
                        $existingGaji->update($dataGaji);
                    } else {
                        Gaji::create($dataGaji);
                    }
                    $gajiGeneratedCount++;
                }
                DB::commit();

                if ($gajiGeneratedCount > 0) {
                    return redirect()->route('admin.gaji.index', ['bulan_tahun' => $bulanTahun])
                                 ->with('success', $gajiGeneratedCount . ' data gaji berhasil dihitung dan disimpan/diperbarui untuk bulan ' . $parsedBulanTahun->isoFormat('MMMM YYYY') . '.');
                } else {
                     return redirect()->route('admin.gaji.index', ['bulan_tahun' => $bulanTahun])
                                 ->with('info', 'Tidak ada data gaji baru yang dihasilkan. Data mungkin sudah ada dan tidak ditimpa.');
                }


            } catch (\Exception $e) {
                DB::rollBack();
                // Log::error($e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menghitung gaji: ' . $e->getMessage());
            }
        }

        public function cetakSlip(Gaji $gaji)
        {
            // $gaji sudah di-resolve oleh Route Model Binding
            $gaji->load('karyawan.user'); // Eager load relasi
            return view('admin.gaji.slip', compact('gaji'));
        }

        public function updateStatusPembayaran(Request $request, Gaji $gaji)
        {
            $request->validate([
                'status_pembayaran' => 'required|in:Belum Dibayar,Sudah Dibayar',
            ]);

            $gaji->status_pembayaran = $request->status_pembayaran;
            if ($request->status_pembayaran == 'Sudah Dibayar' && !$gaji->tanggal_pembayaran) {
                $gaji->tanggal_pembayaran = now();
            } elseif ($request->status_pembayaran == 'Belum Dibayar') {
                $gaji->tanggal_pembayaran = null;
            }
            $gaji->save();

            return redirect()->back()->with('success', 'Status pembayaran gaji berhasil diperbarui.');
        }
    }