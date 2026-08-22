<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $usernameInput = trim($credentials['username']);
        $remember = $request->boolean('remember');

        // Cari user berdasarkan username asli atau alias khusus ('admin' -> Bu Susanti, 'kades' -> Pa Daday Daryat)
        $user = \App\Models\User::where(function ($q) use ($usernameInput) {
            $q->where('username', $usernameInput);
            if ($usernameInput === 'admin') {
                $q->orWhere('role', \App\Enums\UserRole::ADMIN->value);
            } elseif ($usernameInput === 'kades') {
                $q->orWhere('role', \App\Enums\UserRole::KEPALA_DESA->value);
            }
        })->where('is_active', true)->first();

        if ($user && $user->password && \Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
            // Pastikan hanya role kedinasan (Admin Sekdes & Kepala Desa) yang dapat login ke Admin Panel
            if (!in_array($user->role, [\App\Enums\UserRole::ADMIN->value, \App\Enums\UserRole::KEPALA_DESA->value])) {
                throw ValidationException::withMessages([
                    'username' => 'Akun ini terdaftar sebagai staf perangkat desa. Silakan masuk melalui halaman login staf untuk presensi.',
                ]);
            }

            Auth::login($user, $remember);
            $request->session()->regenerate();

            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'role' => $user->role,
                'aktivitas' => 'Login ke sistem SADI Panel Kedinasan',
                'modul' => 'Autentikasi',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->intended(route('dashboard'));
        }

        AuditLog::create([
            'user_name' => $credentials['username'],
            'role' => 'Guest',
            'aktivitas' => 'Gagal login (username/password salah)',
            'modul' => 'Autentikasi',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        throw ValidationException::withMessages([
            'username' => 'Username atau password yang Anda masukkan tidak sesuai.',
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            AuditLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'role' => $user->role,
                'aktivitas' => 'Logout dari sistem SADI',
                'modul' => 'Autentikasi',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
