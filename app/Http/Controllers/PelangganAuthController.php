<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class PelangganAuthController extends Controller
{
    /**
     * Tampilkan form login pelanggan.
     */
    public function showLoginForm()
    {
        // Sesuaikan dengan view-mu
        return view('auth.pelanggan-login'); // contoh: resources/views/auth/pelanggan-login.blade.php
    }

    /**
     * Proses login pelanggan (email ATAU username).
     * - Set last_login_at dengan zona waktu Asia/Jakarta
     */
    public function login(Request $r)
    {
        $r->validate([
            'identifier' => 'required', // email atau username
            'password'   => 'required',
        ]);

        // Cari pelanggan berdasar email/username
        $pelanggan = filter_var($r->identifier, FILTER_VALIDATE_EMAIL)
            ? Pelanggan::where('email', $r->identifier)->first()
            : Pelanggan::where('username', $r->identifier)->first();

        if (!$pelanggan) {
            return back()->withErrors(['identifier' => 'Email/Username tidak ditemukan.']);
        }
        if (!Hash::check($r->password, $pelanggan->password)) {
            return back()->withErrors(['password' => 'Kata sandi salah.']);
        }

        // Login via guard 'pelanggan'
        Auth::guard('pelanggan')->login($pelanggan);

        // ⬇️ Set last_login_at (WIB)
        $pelanggan->forceFill([
            'last_login_at' => Carbon::now('Asia/Jakarta'),
        ])->save();

        // Regenerasi sesi & redirect
        $r->session()->regenerate();
        return redirect()->intended(route('customer.home'));
    }

    /**
     * Tampilkan form register pelanggan.
     */
    public function showRegisterForm()
    {
        return view('auth.register'); // atau 'pelanggan.auth.register' sesuai strukturmu
    }

    /**
     * Proses register pelanggan.
     * - Setelah dibuat, langsung login & set last_login_at (WIB)
     */
    public function register(Request $r)
    {
        $data = $r->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'no_hp'          => 'required|string|max:20',
            'username'       => 'required|string|max:50|alpha_dash|unique:pelanggan,username',
            'email'          => 'required|email|unique:pelanggan,email',
            'password'       => 'required|string|min:6',
            'alamat'         => 'nullable|string|max:255',
        ]);

        // Buat pelanggan (ID PLGxxxxxx otomatis dari Model::booted)
        $pel = DB::transaction(function () use ($data) {
            return Pelanggan::create([
                'nama_pelanggan' => $data['nama_pelanggan'],
                'no_hp'          => $data['no_hp'],
                'username'       => $data['username'],
                'email'          => $data['email'],
                // meski model punya mutator, hashing eksplisit tetap aman
                'password'       => Hash::make($data['password']),
                'alamat'         => $data['alamat'] ?? null,
            ]);
        });

        // Login via guard 'pelanggan'
        Auth::guard('pelanggan')->login($pel);

        // ⬇️ Set last_login_at (WIB) saat pertama kali login
        $pel->forceFill([
            'last_login_at' => Carbon::now('Asia/Jakarta'),
        ])->save();

        $r->session()->regenerate();
        return redirect()->route('customer.home')->with('success', 'Registrasi berhasil!');
    }

    /**
     * Logout pelanggan.
     */
    public function logout(Request $r)
    {
        Auth::guard('pelanggan')->logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return redirect()->route('pelanggan.login')->with('success', 'Berhasil logout.');
    }
}
