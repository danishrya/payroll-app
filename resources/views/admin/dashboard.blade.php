@extends('layouts.app')

    @section('title', 'Admin Dashboard')

    @section('content')
    <div class="space-y-8">
        <h1 class="text-3xl font-semibold text-gray-800">Admin Dashboard</h1>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-500 text-white mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Karyawan</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalKaryawan }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-500 text-white mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Absensi Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $absensiHariIni }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-500 text-white mr-4">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Gaji Bulan Ini</p>
                        <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalGajiBulanIni, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Links -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Akses Cepat</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                <a href="{{ route('admin.karyawan.index') }}" class="block p-4 bg-primary-50 hover:bg-primary-100 text-primary-700 rounded-lg text-center transition-colors duration-300">
                    <span class="text-lg font-medium">Kelola Karyawan</span>
                </a>
                <a href="{{ route('admin.absensi.index') }}" class="block p-4 bg-primary-50 hover:bg-primary-100 text-primary-700 rounded-lg text-center transition-colors duration-300">
                    <span class="text-lg font-medium">Rekap Absensi</span>
                </a>
                <a href="{{ route('admin.gaji.index') }}" class="block p-4 bg-primary-50 hover:bg-primary-100 text-primary-700 rounded-lg text-center transition-colors duration-300">
                    <span class="text-lg font-medium">Penggajian</span>
                </a>
                <a href="{{ route('admin.gaji.show_hitung_form') }}" class="block p-4 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg text-center transition-colors duration-300">
                    <span class="text-lg font-medium">Hitung Gaji Baru</span>
                </a>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Karyawan Baru Ditambahkan</h2>
                @if($karyawanBaru->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($karyawanBaru as $k)
                    <li class="py-3 flex justify-between items-center">
                        <div>
                            <p class="text-md font-medium text-gray-900">{{ $k->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $k->jabatan }} - Bergabung: {{ $k->tanggal_bergabung ? $k->tanggal_bergabung->isoFormat('D MMM YYYY') : 'N/A' }}</p>
                        </div>
                        <a href="{{ route('admin.karyawan.edit', $k->id) }}" class="text-sm text-primary-600 hover:text-primary-800">Detail</a>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-gray-500">Tidak ada karyawan baru baru ini.</p>
                @endif
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Absensi Terbaru</h2>
                 @if($absensiTerbaru->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($absensiTerbaru as $absen)
                    <li class="py-3">
                        <p class="text-md font-medium text-gray-900">{{ $absen->karyawan->user->name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $absen->tanggal_absensi->isoFormat('D MMM YYYY') }} -
                            Masuk: {{ $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') : '-' }} /
                            Pulang: {{ $absen->jam_pulang ? \Carbon\Carbon::parse($absen->jam_pulang)->format('H:i') : '-' }}
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($absen->status_kehadiran == 'Hadir') bg-green-100 text-green-800
                                @elseif(str_contains($absen->status_kehadiran, 'Belum Pulang')) bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $absen->status_kehadiran }}
                            </span>
                        </p>
                    </li>
                    @endforeach
                </ul>
                @else
                 <p class="text-gray-500">Tidak ada aktivitas absensi terbaru.</p>
                @endif
            </div>
        </div>
    </div>
    @endsection