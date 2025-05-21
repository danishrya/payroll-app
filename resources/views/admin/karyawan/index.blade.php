@extends('layouts.app')

    @section('title', 'Kelola Karyawan')

    @section('content')
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-2 sm:space-y-0">
            <h1 class="text-2xl font-semibold text-gray-800">Daftar Karyawan</h1>
            <div class="flex space-x-2">
                 <form method="GET" action="{{ route('admin.karyawan.index') }}" class="flex">
                    <input type="text" name="search" placeholder="Cari karyawan..." value="{{ $search ?? '' }}"
                           class="form-input rounded-l-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-r-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        Cari
                    </button>
                </form>
                <a href="{{ route('admin.karyawan.create') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-150 ease-in-out">
                    Tambah Karyawan
                </a>
            </div>
        </div>

        @if($karyawans->isEmpty() && !$search)
            <p class="text-gray-600">Belum ada data karyawan. <a href="{{ route('admin.karyawan.create') }}" class="text-primary-600 hover:underline">Tambahkan sekarang</a>.</p>
        @elseif($karyawans->isEmpty() && $search)
             <p class="text-gray-600">Tidak ada karyawan yang cocok dengan pencarian "{{ $search }}".</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gaji Pokok</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($karyawans as $karyawan)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $karyawan->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $karyawan->user->username }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $karyawan->nip ?: '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $karyawan->jabatan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($karyawan->gaji_pokok, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('admin.karyawan.edit', $karyawan->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <form action="{{ route('admin.karyawan.destroy', $karyawan->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini? Tindakan ini juga akan menghapus user terkait dan semua data absensi serta gaji.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
             <div class="mt-4">
                {{ $karyawans->appends(['search' => $search])->links() }}
            </div>
        @endif
    </div>
    @endsection