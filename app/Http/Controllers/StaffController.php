<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Staff;
use Illuminate\Support\Carbon;

class StaffController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'password'   => 'required',
        ]);

        // Cari staff berdasar email/username
        $staff = filter_var($request->identifier, FILTER_VALIDATE_EMAIL)
            ? Staff::where('email', $request->identifier)->first()
            : Staff::where('username', $request->identifier)->first();

        if (!$staff) {
            return back()->withErrors(['identifier' => 'Username atau Email tidak ditemukan.']);
        }
        if (!Hash::check($request->password, $staff->password)) {
            return back()->withErrors(['password' => 'Kredensial salah.']);
        }

        // Login-kan via guard 'staff'
        Auth::guard('staff')->login($staff);

        // ⬇️ SET last_login_at (WIB) — TARUH DI SINI
        $staff->forceFill([
            'last_login_at' => Carbon::now('Asia/Jakarta'),
        ])->save();

        // regen session & redirect
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form')->with('success', 'Berhasil logout.');
    }
}
