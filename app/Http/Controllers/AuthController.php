<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use App\Models\User; // Tambahkan ini
    use Illuminate\Support\Facades\Hash; // Tambahkan ini

    class AuthController extends Controller
    {
        public function showLoginForm()
        {
            return view('auth.login');
        }

        public function login(Request $request)
        {
            $credentials = $request->validate([
                'username' => ['required', 'string'],
                'password' => ['required', 'string'],
            ]);

            if (Auth::attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();

                $user = Auth::user();
                if ($user->isAdmin()) {
                    return redirect()->intended(route('admin.dashboard'));
                } elseif ($user->isKaryawan()) {
                    // Pastikan karyawan memiliki data terkait di tabel karyawans
                    if (!$user->karyawan) {
                         Auth::logout();
                         $request->session()->invalidate();
                         $request->session()->regenerateToken();
                         return back()->withErrors([
                             'username' => 'Data karyawan tidak ditemukan. Hubungi administrator.',
                         ])->onlyInput('username');
                    }
                    return redirect()->intended(route('karyawan.dashboard'));
                }
                // Default fallback jika role tidak terdefinisi (seharusnya tidak terjadi)
                return redirect('/');
            }

            return back()->withErrors([
                'username' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
            ])->onlyInput('username');
        }

        public function logout(Request $request)
        {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/');
        }

        // Fungsi register bisa ditambahkan di sini jika dibutuhkan,
        // atau pendaftaran karyawan dilakukan oleh admin
    }