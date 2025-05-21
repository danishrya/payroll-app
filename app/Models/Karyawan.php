<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Karyawan extends Model
    {
        use HasFactory;

        protected $fillable = [
            'user_id',
            'nip',
            'jabatan',
            'gaji_pokok',
            'alamat',
            'no_telepon',
            'tanggal_bergabung',
        ];

        protected $casts = [
            'gaji_pokok' => 'decimal:2',
            'tanggal_bergabung' => 'date',
        ];

        // Relasi: Karyawan dimiliki oleh satu User
        public function user()
        {
            return $this->belongsTo(User::class);
        }

        // Relasi: Seorang Karyawan memiliki banyak Absensi
        public function absensi()
        {
            return $this->hasMany(Absensi::class);
        }

        // Relasi: Seorang Karyawan memiliki banyak Gaji
        public function gaji()
        {
            return $this->hasMany(Gaji::class);
        }

        // Cek apakah karyawan sudah absen masuk hari ini
        public function sudahAbsenMasukHariIni()
        {
            return $this->absensi()
                        ->whereDate('tanggal_absensi', today())
                        ->whereNotNull('jam_masuk')
                        ->exists();
        }

        // Cek apakah karyawan sudah absen pulang hari ini
        public function sudahAbsenPulangHariIni()
        {
            return $this->absensi()
                        ->whereDate('tanggal_absensi', today())
                        ->whereNotNull('jam_pulang')
                        ->exists();
        }

        // Mendapatkan absensi hari ini
        public function absensiHariIni()
        {
            return $this->absensi()
                        ->whereDate('tanggal_absensi', today())
                        ->first();
        }
    }