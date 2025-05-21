  <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Nama lengkap user (bisa admin atau display name karyawan)
                $table->string('username')->unique(); // Untuk login
                $table->string('email')->unique()->nullable(); // Email bisa nullable
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->enum('role', ['admin', 'karyawan'])->default('karyawan');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('users');
        }
    };