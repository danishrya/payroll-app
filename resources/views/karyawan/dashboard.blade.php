 @extends('layouts.app')

    @section('title', 'Dashboard Karyawan')

    @section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-3xl font-semibold text-gray-800 mb-2">Selamat Datang, {{ $karyawan->user->name }}!</h1>
        <p class="text-gray-600 mb-6">Ini adalah dashboard absensi Anda.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-blue-50 p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold text-blue-700 mb-3">Informasi Karyawan</h2>
                <p><strong>NIP:</strong> {{ $karyawan->nip ?: '-' }}</p>
                <p><strong>Jabatan:</strong> {{ $karyawan->jabatan }}</p>
                <p><strong>Tanggal Bergabung:</strong> {{ $karyawan->tanggal_bergabung ? $karyawan->tanggal_bergabung->format('d F Y') : '-' }}</p>
            </div>
            <div class="bg-green-50 p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold text-green-700 mb-3">Status Absensi Hari Ini ({{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }})</h2>
                @if($absensiHariIni)
                    <p><strong>Jam Masuk:</strong> {{ $absensiHariIni->jam_masuk ? \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i:s') : 'Belum Absen Masuk' }}</p>
                    <p><strong>Jam Pulang:</strong> {{ $absensiHariIni->jam_pulang ? \Carbon\Carbon::parse($absensiHariIni->jam_pulang)->format('H:i:s') : 'Belum Absen Pulang' }}</p>
                    <p><strong>Status:</strong> <span class="font-medium {{ $absensiHariIni->status_kehadiran == 'Hadir' ? 'text-green-600' : 'text-yellow-600' }}">{{ $absensiHariIni->status_kehadiran }}</span></p>
                @else
                    <p class="text-gray-500">Belum ada data absensi untuk hari ini.</p>
                @endif
            </div>
        </div>

        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mb-6">
            @if(!$sudahAbsenMasuk)
            <form action="{{ route('karyawan.presensi.masuk') }}" method="POST">
                @csrf
                <button type="submit" class="w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg shadow transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" /></svg>
                    Presensi Masuk
                </button>
            </form>
            @else
            <button type="button" class="w-full sm:w-auto bg-gray-400 text-white font-bold py-3 px-6 rounded-lg shadow cursor-not-allowed" disabled>
                Sudah Presensi Masuk
            </button>
            @endif

            @if($sudahAbsenMasuk && !$sudahAbsenPulang)
            <form action="{{ route('karyawan.presensi.pulang') }}" method="POST">
                @csrf
                <button type="submit" class="w-full sm:w-auto bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-lg shadow transition duration-150 ease-in-out">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" transform="rotate(180 10 10)" /></svg>
                    Presensi Pulang
                </button>
            </form>
            @elseif ($sudahAbsenMasuk && $sudahAbsenPulang)
             <button type="button" class="w-full sm:w-auto bg-gray-400 text-white font-bold py-3 px-6 rounded-lg shadow cursor-not-allowed" disabled>
                Sudah Presensi Pulang
            </button>
            @else
            <button type="button" class="w-full sm:w-auto bg-gray-300 text-gray-500 font-bold py-3 px-6 rounded-lg shadow cursor-not-allowed" disabled>
                Presensi Pulang (Belum Masuk)
            </button>
            @endif
        </div>

        <div>
            <a href="{{ route('karyawan.riwayat.absensi') }}" class="inline-block bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-lg shadow transition duration-150 ease-in-out">
                Lihat Riwayat Absensi Saya
            </a>
        </div>
    </div>
    @endsection