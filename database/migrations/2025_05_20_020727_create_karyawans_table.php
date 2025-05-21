 <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('karyawans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relasi ke tabel users
                $table->string('nip')->unique()->nullable(); // Nomor Induk Pegawai
                $table->string('jabatan');
                $table->decimal('gaji_pokok', 15, 2);
                $table->string('alamat')->nullable();
                $table->string('no_telepon')->nullable();
                $table->date('tanggal_bergabung')->nullable();
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('karyawans');
        }
    };