<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * LoginController
 * 
 * Pengganti controller CI3: application/controllers/Login.php
 * 
 * Konvensi mengikuti rules.md:
 * - Nama file: UpperCamelCase
 * - Method: camelCase
 * - Prefix method: get*, find*, save*, update*, delete*
 */
class LoginController extends Controller
{
    /**
     * Tampilkan form login
     * Pengganti: Login::index() di CI3
     */
    public function index()
    {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    /**
     * Proses login
     * Pengganti: Login::proses() di CI3
     */
    public function proses(Request $request): RedirectResponse
    {
        $username = $request->input('username');
        $password = $request->input('password');

        \Log::info("LOGIN ATTEMPT: username=[" . ($username ?: "null") . "] password_set=" . (!empty($password) ? "yes" : "NO") . " ip=" . $request->ip());

        // Cari user berdasarkan username (CI3 login menggunakan USERNAME)
        $user = User::where('USERNAME', $username)->first();

        if (!$user) {
            \Log::info("LOGIN FAIL: user not found for username=[" . ($username ?: "null") . "]");
            session()->flash('alert', 'error-login');
            return redirect()->route('login');
        }

        $hashMatch = Hash::check($password, $user->PASSWORD);
        if (!$hashMatch) {
            \Log::info("LOGIN FAIL: hash mismatch for user=[" . $username . "] stored_hash=" . substr($user->PASSWORD, 0, 20));
            session()->flash('alert', 'error-login');
            return redirect()->route('login');
        }

        \Log::info("LOGIN SUCCESS: user=[" . $username . "] ROLE_ID=" . ($user->ROLE_ID ?? "null"));

        // Set session (sama seperti CI3 $this->session->set_userdata)
        session(['user_logged' => $user]);
        \Auth::login($user);

        // Update last login (best-effort, jangan blokir login jika gagal)
        try {
            $user->forceFill(['last_login' => now()])->save();
        } catch (\Throwable $e) {
            \Log::warning('Failed to update last_login: ' . $e->getMessage());
        }

        return redirect()->route('admin.dashboard');
    }

    /**
     * Logout
     * Pengganti: Login::logout() di CI3
     */
    public function logout(): RedirectResponse
    {
        $this->guard()->logout();
        session()->flush();

        return redirect()->route('login');
    }

    private function guard()
    {
        return auth()->guard('web');
    }
}
