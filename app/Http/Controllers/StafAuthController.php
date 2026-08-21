<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Validation\ValidationException;

class StafAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('staf.beranda');
        }

        return view('staf.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
        ]);

        $username = trim($request->username);

        $user = User::where('username', $username)
            ->whereIn('role', [\App\Enums\UserRole::PERANGKAT->value, \App\Enums\UserRole::STAF->value, \App\Enums\UserRole::KEPALA_DESA->value, \App\Enums\UserRole::ADMIN->value])
            ->where('is_active', true)
            ->first();

        if (!$user) {
            AuditLog::create([
                'user_name' => $username,
                'role' => 'Staf / Guest',
                'aktivitas' => "Gagal masuk portal staf (username '{$username}' tidak ditemukan atau nonaktif)",
                'modul' => 'Portal Staf',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            throw ValidationException::withMessages([
                'username' => 'Akun dengan username tersebut tidak ditemukan atau sedang dinonaktifkan.',
            ]);
        }

        // Login user tanpa password
        Auth::login($user, true);
        $request->session()->regenerate();

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role' => $user->role,
            'aktivitas' => 'Masuk ke Portal Presensi Staf Desa (Username-Only)',
            'modul' => 'Portal Staf',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('staf.beranda');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            AuditLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'role' => $user->role,
                'aktivitas' => 'Keluar dari Portal Presensi Staf Desa',
                'modul' => 'Portal Staf',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staf.login')->with('success', 'Anda telah berhasil keluar dari akun.');
    }
}
