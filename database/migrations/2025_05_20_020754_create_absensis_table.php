<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('absensis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
                $table->date('tanggal_absensi');
                $table->time('jam_masuk')->nullable();
                $table->time('jam_pulang')->nullable();
                $table->string('status_kehadiran')->default('Belum Absen'); // Misal: Hadir, Izin, Sakit, Alpha, Belum Absen
                $table->string('keterangan')->nullable(); // Untuk catatan jika izin/sakit
                $table->timestamps();

                $table->unique(['karyawan_id', 'tanggal_absensi']); // Karyawan hanya bisa absen sekali per hari
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('absensis');
        }
    };