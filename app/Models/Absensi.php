<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Absensi extends Model
    {
        use HasFactory;

        protected $fillable = [
            'karyawan_id',
            'tanggal_absensi',
            'jam_masuk',
            'jam_pulang',
            'status_kehadiran',
            'keterangan',
        ];

        protected $casts = [
            'tanggal_absensi' => 'date',
        ];

        // Relasi: Absensi dimiliki oleh satu Karyawan
        public function karyawan()
        {
            return $this->belongsTo(Karyawan::class);
        }
    }

