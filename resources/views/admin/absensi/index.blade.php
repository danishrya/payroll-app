@extends('layouts.app')

    @section('title', 'Rekap Absensi Karyawan')

    @section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-3 sm:space-y-0">
            <h1 class="text-2xl font-semibold text-gray-800">Rekap Absensi Karyawan</h1>
            {{-- Tombol export (jika diimplementasikan)
            <a href="{{ route('admin.absensi.export', ['bulan' => $selectedMonth, 'karyawan_id' => $selectedKaryawan]) }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg shadow">
                Export ke Excel
            </a>
            --}}
        </div>

        <form method="GET" action="{{ route('admin.absensi.index') }}" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label for="bulan" class="block text-sm font-medium text-gray-700">Pilih Bulan:</label>
                    <input type="month" name="bulan" id="bulan" value="{{ $selectedMonth }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                </div>
                <div>
                    <label for="karyawan_id" class="block text-sm font-medium text-gray-700">Pilih Karyawan (Opsional):</label>
                    <select name="karyawan_id" id="karyawan_id"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <option value="">Semua Karyawan</option>
                        @foreach($karyawans as $k)
                            <option value="{{ $k->id }}" {{ $selectedKaryawan == $k->id ? 'selected' : '' }}>
                                {{ $k->user->name }} ({{ $k->nip ?: $k->jabatan}})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-1">
                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out">
                        Filter Data
                    </button>
                </div>
            </div>
        </form>

        @if($absensis->isEmpty())
            <p class="text-gray-600">Tidak ada data absensi untuk filter yang dipilih.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Pulang</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($absensis as $absensi)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $absensi->karyawan->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $absensi->tanggal_absensi->isoFormat('dddd, D MMM YYYY') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $absensi->jam_masuk ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i:s') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $absensi->jam_pulang ? \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i:s') : '-' }}</td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($absensi->status_kehadiran == 'Hadir') bg-green-100 text-green-800
                                    @elseif(str_contains($absensi->status_kehadiran, 'Belum Pulang')) bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $absensi->status_kehadiran }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $absensi->keterangan ?: '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $absensis->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
    @endsection