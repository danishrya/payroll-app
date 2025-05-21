<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Gaji extends Model
    {
        use HasFactory;

        protected $fillable = [
            'karyawan_id',
            'bulan_tahun',
            'gaji_pokok',
            'jumlah_hari_kerja',
            'jumlah_kehadiran',
            'jumlah_ketidakhadiran',
            'potongan_ketidakhadiran',
            'gaji_bersih',
            'tanggal_pembayaran',
            'status_pembayaran',
        ];

        protected $casts = [
            'gaji_pokok' => 'decimal:2',
            'potongan_ketidakhadiran' => 'decimal:2',
            'gaji_bersih' => 'decimal:2',
            'tanggal_pembayaran' => 'date',
        ];

        // Relasi: Gaji dimiliki oleh satu Karyawan
        public function karyawan()
        {
            return $this->belongsTo(Karyawan::class);
        }
    }