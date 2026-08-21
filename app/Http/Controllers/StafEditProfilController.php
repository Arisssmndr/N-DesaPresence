<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StafEditProfilController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        return view('staf.edit-profil', compact('user', 'pegawai'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        $request->validate([
            'username' => 'required|string|max:50|alpha_dash|unique:users,username,' . $user->id,
            'nama_lengkap' => 'required|string|max:100',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'no_hp' => 'nullable|string|max:20',
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string|max:255',
        ]);

        $userData = [
            'username' => strtolower($request->username),
            'name' => $request->nama_lengkap,
        ];

        $pegawaiData = [
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp' => $request->no_hp,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
        ];

        // Handle upload foto profil
        if ($request->hasFile('foto_profil')) {
            $path = $request->file('foto_profil')->store('foto-profil', 'public');
            $userData['foto_profil'] = $path;
            $pegawaiData['foto_profil'] = $path;
        }

        // Update User
        $user->update($userData);

        // Update Pegawai
        if ($pegawai) {
            $pegawai->update($pegawaiData);
        }

        return redirect()->route('staf.profil')->with('success', 'Profil dan foto Anda berhasil diperbarui.');
    }
}
