 <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('gajis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
                $table->string('bulan_tahun'); // Format: YYYY-MM, misal "2023-10"
                $table->decimal('gaji_pokok', 15, 2);
                $table->integer('jumlah_hari_kerja')->default(0);
                $table->integer('jumlah_kehadiran')->default(0);
                $table->integer('jumlah_ketidakhadiran')->default(0);
                $table->decimal('potongan_ketidakhadiran', 15, 2)->default(0);
                $table->decimal('gaji_bersih', 15, 2);
                $table->date('tanggal_pembayaran')->nullable();
                $table->enum('status_pembayaran', ['Belum Dibayar', 'Sudah Dibayar'])->default('Belum Dibayar');
                $table->timestamps();

                $table->unique(['karyawan_id', 'bulan_tahun']); // Satu entri gaji per karyawan per bulan
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('gajis');
        }
    };