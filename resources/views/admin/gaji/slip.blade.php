 <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Slip Gaji - {{ $gaji->karyawan->user->name }} - {{ \Carbon\Carbon::createFromFormat('Y-m', $gaji->bulan_tahun)->isoFormat('MMMM YYYY') }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background-color: #fff; }
            .container { max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; background-color: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
            .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
            .header h1 { margin: 0; font-size: 24px; color: #333; }
            .header p { margin: 5px 0 0; font-size: 14px; color: #555; }
            .content-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            .content-table th, .content-table td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 14px; }
            .content-table th { background-color: #f9f9f9; font-weight: bold; color: #333; }
            .content-table tr:nth-child(even) td { background-color: #f2f2f2; }
            .text-right { text-align: right !important; }
            .font-bold { font-weight: bold; }
            .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #eee; font-size: 12px; color: #777; }
            .footer .signature-area { margin-top: 40px; display: flex; justify-content: space-between; }
            .footer .signature { width: 45%; text-align: center; }
            .footer .signature p { margin-bottom: 50px; } /* Space for signature */

            @media print {
                body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                .no-print { display: none; }
                .container { box-shadow: none; border: none; margin: 0; max-width: 100%; }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>SLIP GAJI KARYAWAN</h1>
                <p>{{-- Nama Perusahaan Anda --}} PT. Jaya Abadi Makmur Sentosa</p>
                <p>{{-- Alamat Perusahaan --}} Jl. Industri No. 123, Kota Contoh, Indonesia</p>
            </div>

            <table class="content-table" style="margin-bottom: 30px;">
                <tr>
                    <th style="width: 30%;">Nama Karyawan</th>
                    <td style="width: 70%;">{{ $gaji->karyawan->user->name }}</td>
                </tr>
                <tr>
                    <th>NIP / ID</th>
                    <td>{{ $gaji->karyawan->nip ?: $gaji->karyawan->id }}</td>
                </tr>
                <tr>
                    <th>Jabatan</th>
                    <td>{{ $gaji->karyawan->jabatan }}</td>
                </tr>
                <tr>
                    <th>Periode Gaji</th>
                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $gaji->bulan_tahun)->isoFormat('MMMM YYYY') }}</td>
                </tr>
                <tr>
                    <th>Tanggal Cetak</th>
                    <td>{{ now()->isoFormat('D MMMM YYYY') }}</td>
                </tr>
            </table>

            <h2 class="text-lg font-semibold mb-2" style="color: #333; border-bottom: 1px solid #eee; padding-bottom: 5px;">Rincian Pendapatan</h2>
            <table class="content-table">
                <tr>
                    <td style="width: 70%;">Gaji Pokok</td>
                    <td class="text-right font-bold">Rp {{ number_format($gaji->gaji_pokok, 2, ',', '.') }}</td>
                </tr>
                {{-- Tambahkan item pendapatan lain jika ada (Tunjangan, dll) --}}
                {{-- <tr>
                    <td>Tunjangan Jabatan</td>
                    <td class="text-right font-bold">Rp {{ number_format(0, 2, ',', '.') }}</td>
                </tr> --}}
                <tr style="background-color: #e9f5e9 !important;">
                    <td class="font-bold">Total Pendapatan Kotor</td>
                    <td class="text-right font-bold">Rp {{ number_format($gaji->gaji_pokok, 2, ',', '.') }}</td> {{-- Sesuaikan jika ada pendapatan lain --}}
                </tr>
            </table>

            <h2 class="text-lg font-semibold mt-6 mb-2" style="color: #333; border-bottom: 1px solid #eee; padding-bottom: 5px;">Rincian Potongan</h2>
            <table class="content-table">
                <tr>
                    <td style="width: 70%;">Potongan Ketidakhadiran ({{ $gaji->jumlah_ketidakhadiran }} hari x Rp {{ number_format($gaji->potongan_ketidakhadiran / ($gaji->jumlah_ketidakhadiran ?: 1), 2, ',', '.') }})</td>
                    <td class="text-right font-bold">Rp {{ number_format($gaji->potongan_ketidakhadiran, 2, ',', '.') }}</td>
                </tr>
                {{-- Tambahkan item potongan lain jika ada (BPJS, PPh, dll) --}}
                 {{-- <tr>
                    <td>Potongan BPJS</td>
                    <td class="text-right font-bold">Rp {{ number_format(0, 2, ',', '.') }}</td>
                </tr> --}}
                <tr style="background-color: #ffeeee !important;">
                    <td class="font-bold">Total Potongan</td>
                    <td class="text-right font-bold">Rp {{ number_format($gaji->potongan_ketidakhadiran, 2, ',', '.') }}</td> {{-- Sesuaikan jika ada potongan lain --}}
                </tr>
            </table>

            <table class="content-table" style="margin-top: 20px; background-color: #e6f7ff !important;">
                 <tr>
                    <td class="font-bold text-lg" style="width: 70%; color: #0056b3;">GAJI BERSIH (Take Home Pay)</td>
                    <td class="text-right font-bold text-lg" style="color: #0056b3;">Rp {{ number_format($gaji->gaji_bersih, 2, ',', '.') }}</td>
                </tr>
            </table>

             <div class="mt-6 p-3 bg-gray-50 border border-gray-200 rounded-md text-sm">
                <p class="font-semibold mb-1">Detail Kehadiran Bulan Ini:</p>
                <ul class="list-disc list-inside ml-4">
                    <li>Jumlah Hari Kerja Efektif: {{ $gaji->jumlah_hari_kerja }} hari</li>
                    <li>Jumlah Kehadiran: {{ $gaji->jumlah_kehadiran }} hari</li>
                    <li>Jumlah Ketidakhadiran (dipotong): {{ $gaji->jumlah_ketidakhadiran }} hari</li>
                </ul>
            </div>

            <div class="footer">
                <div class="signature-area">
                    <div class="signature">
                        <p>Diterima oleh,</p>
                        <br><br><br>
                        <p><strong>( {{ $gaji->karyawan->user->name }} )</strong></p>
                    </div>
                    <div class="signature">
                        <p>Disetujui oleh,</p>
                         <br><br><br>
                        <p><strong>( {{-- Nama Pimpinan/HRD --}} Manager HRD )</strong></p>
                    </div>
                </div>
                <p class="text-center mt-4">Slip gaji ini dicetak secara otomatis oleh sistem pada tanggal {{ now()->isoFormat('D MMMM YYYY, HH:mm:ss') }}.</p>
            </div>

            <div class="text-center mt-8 no-print">
                <button onclick="window.print()" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-6 rounded shadow">
                    Cetak Slip Gaji
                </button>
                 <a href="{{ route('admin.gaji.index', ['bulan_tahun' => $gaji->bulan_tahun]) }}" class="ml-2 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded shadow">
                    Kembali
                </a>
            </div>
        </div>
    </body>
    </html>