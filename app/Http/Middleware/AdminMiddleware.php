<?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Symfony\Component\HttpFoundation\Response;

    class AdminMiddleware
    {
        public function handle(Request $request, Closure $next): Response
        {
            if (Auth::check() && Auth::user()->isAdmin()) {
                return $next($request);
            }
            // Jika bukan admin, redirect atau beri error
            Auth::logout(); // Logout jika mencoba akses tanpa hak
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses admin.');
        }
    }