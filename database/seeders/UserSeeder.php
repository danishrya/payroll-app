<?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use App\Models\User;
    use Illuminate\Support\Facades\Hash;

    class UserSeeder extends Seeder
    {
        public function run(): void
        {
            // Admin User
            User::create([
                'name' => 'Admin Utama',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'), // Ganti dengan password yang kuat
                'role' => 'admin',
            ]);

            // Contoh Karyawan User (opsional, bisa dibuat via admin panel nanti)
            $karyawanUser = User::create([
                'name' => 'Budi Santoso',
                'username' => 'budi',
                'email' => 'budi@gmail.com',
                'password' => Hash::make('123123123'),
                'role' => 'karyawan',
            ]);

            if ($karyawanUser) {
                \App\Models\Karyawan::create([
                    'user_id' => $karyawanUser->id,
                    'nip' => 'K001',
                    'jabatan' => 'Staff IT',
                    'gaji_pokok' => 5000000,
                    'tanggal_bergabung' => now()->subYear(),
                ]);
            }
        }
    }