<?php

    namespace App\Models;

    use Illuminate\Contracts\Auth\MustVerifyEmail;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Laravel\Sanctum\HasApiTokens; // Jika menggunakan Sanctum untuk API

    class User extends Authenticatable
    {
        use HasApiTokens, HasFactory, Notifiable;

        protected $fillable = [
            'name',
            'username',
            'email',
            'password',
            'role',
        ];

        protected $hidden = [
            'password',
            'remember_token',
        ];

        protected $casts = [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];

        // Relasi: Seorang User bisa jadi satu Karyawan
        public function karyawan()
        {
            return $this->hasOne(Karyawan::class);
        }

        public function isAdmin()
        {
            return $this->role === 'admin';
        }

        public function isKaryawan()
        {
            return $this->role === 'karyawan';
        }
    }