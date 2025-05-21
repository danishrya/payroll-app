<?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Symfony\Component\HttpFoundation\Response;

    class KaryawanMiddleware
    {
        public function handle(Request $request, Closure $next): Response
        {
            if (Auth::check() && Auth::user()->isKaryawan()) {
                // Pastikan juga user karyawan ini punya entri di tabel karyawans
                if (Auth::user()->karyawan) {
                    return $next($request);
                }
                // Jika data karyawan tidak ditemukan, logout dan beri pesan
                Auth::logout();
                return redirect()->route('login')->with('error', 'Data profil karyawan Anda tidak lengkap. Hubungi Admin.');
            }
            // Jika bukan karyawan, redirect atau beri error
            Auth::logout();
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses karyawan.');
        }
    }
