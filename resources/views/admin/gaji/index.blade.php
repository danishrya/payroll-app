@extends('layouts.app')

    @section('title', 'Data Penggajian')

    @section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-3 sm:space-y-0">
            <h1 class="text-2xl font-semibold text-gray-800">Data Penggajian</h1>
            <a href="{{ route('admin.gaji.show_hitung_form') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-150 ease-in-out">
                Hitung Gaji Baru
            </a>
        </div>

        <form method="GET" action="{{ route('admin.gaji.index') }}" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="bulan_tahun" class="block text-sm font-medium text-gray-700">Pilih Bulan & Tahun:</label>
                    <input type="month" name="bulan_tahun" id="bulan_tahun" value="{{ $selectedMonth }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                </div>
                <div>
                    <label for="karyawan_id" class="block text-sm font-medium text-gray-700">Karyawan (Opsional):</label>
                    <select name="karyawan_id" id="karyawan_id"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <option value="">Semua Karyawan</option>
                        @foreach($karyawans as $k)
                            <option value="{{ $k->id }}" {{ $selectedKaryawan == $k->id ? 'selected' : '' }}>
                                {{ $k->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                 <div>
                    <label for="status_pembayaran" class="block text-sm font-medium text-gray-700">Status Pembayaran:</label>
                    <select name="status_pembayaran" id="status_pembayaran"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <option value="">Semua Status</option>
                        <option value="Belum Dibayar" {{ $statusPembayaran == 'Belum Dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                        <option value="Sudah Dibayar" {{ $statusPembayaran == 'Sudah Dibayar' ? 'selected' : '' }}>Sudah Dibayar</option>
                    </select>
                </div>
                <div class="md:col-span-1">
                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2 px-4 rounded-md shadow transition duration-150 ease-in-out">
                        Filter
                    </button>
                </div>
            </div>
        </form>

        @if($gajis->isEmpty())
            <p class="text-gray-600">Tidak ada data gaji untuk filter yang dipilih. <a href="{{ route('admin.gaji.show_hitung_form') }}" class="text-primary-600 hover:underline">Hitung gaji sekarang</a>.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan/Tahun</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gaji Pokok</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Potongan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gaji Bersih</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($gajis as $gaji)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $gaji->karyawan->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::createFromFormat('Y-m', $gaji->bulan_tahun)->isoFormat('MMMM YYYY') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500">Rp {{ number_format($gaji->potongan_ketidakhadiran, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">Rp {{ number_format($gaji->gaji_bersih, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <form action="{{ route('admin.gaji.update_status', $gaji->id) }}" method="POST" class="inline-flex items-center">
                                    @csrf
                                    <select name="status_pembayaran" onchange="this.form.submit()"
                                            class="text-xs border-gray-300 rounded-md shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50
                                            {{ $gaji->status_pembayaran == 'Sudah Dibayar' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        <option value="Belum Dibayar" {{ $gaji->status_pembayaran == 'Belum Dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                                        <option value="Sudah Dibayar" {{ $gaji->status_pembayaran == 'Sudah Dibayar' ? 'selected' : '' }}>Sudah Dibayar</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.gaji.slip', $gaji->id) }}" target="_blank" class="text-primary-600 hover:text-primary-900">Cetak Slip</a>
                                {{-- Tambah tombol edit jika diperlukan untuk mengedit detail gaji manual --}}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $gajis->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
    @endsection