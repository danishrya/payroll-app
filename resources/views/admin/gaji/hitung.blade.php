@extends('layouts.app')

    @section('title', 'Hitung Gaji Karyawan')

    @section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Hitung Gaji Karyawan</h1>
            <a href="{{ route('admin.gaji.index') }}" class="text-sm text-primary-600 hover:text-primary-800">&larr; Kembali ke Data Gaji</a>
        </div>

        <form action="{{ route('admin.gaji.hitung') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="bulan_tahun" class="block text-sm font-medium text-gray-700">Periode Gaji (Bulan & Tahun) <span class="text-red-500">*</span></label>
                    <input type="month" name="bulan_tahun" id="bulan_tahun" value="{{ old('bulan_tahun', now()->format('Y-m')) }}" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                </div>
                <div>
                    <label for="potongan_per_hari" class="block text-sm font-medium text-gray-700">Potongan per Hari Tidak Hadir (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="potongan_per_hari" id="potongan_per_hari" value="{{ old('potongan_per_hari', 50000) }}" required min="0" step="1000"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                </div>
            </div>

            <div>
                <label for="karyawan_ids" class="block text-sm font-medium text-gray-700">Pilih Karyawan (Kosongkan untuk semua karyawan)</label>
                <select name="karyawan_ids[]" id="karyawan_ids" multiple
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm h-40">
                    <option value="all" {{ (is_array(old('karyawan_ids')) && in_array('all', old('karyawan_ids'))) || empty(old('karyawan_ids')) ? 'selected' : '' }}>-- Semua Karyawan --</option>
                    @foreach($karyawans as $karyawan)
                        <option value="{{ $karyawan->id }}" {{ (is_array(old('karyawan_ids')) && in_array($karyawan->id, old('karyawan_ids'))) ? 'selected' : '' }}>
                            {{ $karyawan->user->name }} ({{ $karyawan->nip ?: $karyawan->jabatan }})
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Gunakan Ctrl/Cmd + Klik untuk memilih beberapa karyawan. Jika "-- Semua Karyawan --" dipilih, pilihan lain akan diabaikan.</p>
            </div>

            <div>
                <label for="hari_libur_nasional" class="block text-sm font-medium text-gray-700">Hari Libur Nasional (Format: YYYY-MM-DD, pisahkan dengan koma jika lebih dari satu)</label>
                <input type="text" name="hari_libur_nasional" id="hari_libur_nasional" value="{{ old('hari_libur_nasional') }}"
                       placeholder="Contoh: 2023-12-25,2024-01-01"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">Hari Sabtu & Minggu otomatis tidak dihitung sebagai hari kerja.</p>
            </div>

            <div class="flex items-center">
                <input id="timpa_data" name="timpa_data" type="checkbox" value="yes" class="h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                <label for="timpa_data" class="ml-2 block text-sm text-gray-900">
                    Timpa data gaji yang sudah ada untuk periode dan karyawan yang sama?
                </label>
            </div>


            <div class="pt-5">
                <div class="flex justify-end">
                    <a href="{{ route('admin.gaji.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Batal
                    </a>
                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Hitung dan Simpan Gaji
                    </button>
                </div>
            </div>
        </form>
    </div>
    @endsection