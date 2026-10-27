<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

/**
 * BaseAdminController
 * 
 * Pengganti GLOBAL_Controller dari CI3.
 * Semua controller utama (Admin, Member, Saving, Loan)
 * harus extend dari class ini.
 * 
 * Konvensi mengikuti rules.md:
 * - Nama file: UpperCamelCase
 * - Method: camelCase
 * - Prefix method: get*, find*, save*, update*, delete*
 */
abstract class BaseAdminController extends Controller
{
    /**
     * User yang sedang login
     */
    protected ?User $user = null;

    /**
     * Level/role user saat ini
     */
    protected ?string $userLevel = null;

    /**
     * Nama user saat ini
     */
    protected ?string $userName = null;

    public function __construct()
    {
        // Semua controller yang extend BaseAdminController
        // wajib sudah login (menggantikan cek isNotLogin di CI3)
        $this->middleware(function ($request, $next) {
            if (auth()->guest()) {
                return redirect()->route('login');
            }
            return $next($request);
        });

        $this->refreshUser();
    }

    /**
     * Muat ulang data user dari session (aman dipanggil ulang di setiap request).
     * Di CI3 ini setara dengan $this->session->userdata() yang selalu fresh.
     */
    protected function refreshUser(): void
    {
        $this->user = auth()->user();
        if ($this->user) {
            $this->userLevel = (string)($this->user->ROLE_ID ?? null);
            $this->userName = $this->user->USERNAME ?? null;
        }
    }

    /**
     * Render view Blade dengan data
     * Pengganti $this->load->view($view, $data) di CI3
     * 
     * @param string $view Nama view (tanpa .blade.php)
     * @param array $data Array data yang dikirim ke view
     * @return \Illuminate\View\View
     */
    protected function view(string $view, array $data = [])
    {
        return view($view, $data);
    }

    /**
     * Set flash message
     * Pengganti $this->session->set_flashdata($name, $value) di CI3
     * 
     * @param string $name Kunci flashdata
     * @param string $value Nilai pesan
     */
    protected function alert(string $name, string $value): void
    {
        session()->flash($name, $value);
    }

    /**
     * Ambil data dari request
     * Pengganti $this->input->post($value) di CI3
     * 
     * @param string $key Nama field
     * @return mixed
     */
    protected function post(string $key)
    {
        return request($key);
    }

    /**
     * Ambil semua data POST
     * Pengganti $this->input->post() di CI3
     * 
     * @return array
     */
    protected function postAll(): array
    {
        return request()->all();
    }

    /**
     * Cek apakah user memiliki role tertentu
     * 
     * @param string ...$roles Daftar role ID yang diizinkan
     * @return bool
     */
    protected function hasRole(string ...$roles): bool
    {
        if (!$this->userLevel) {
            return false;
        }
        return in_array($this->userLevel, $roles, true);
    }

    /**
     * Redirect ke halaman sesuai role
     * Pengganti switch-case di Admin::index() CI3
     * 
     * @return RedirectResponse
     */
    protected function dashboardByRole(): RedirectResponse
    {
        $role = $this->userLevel;

        switch ($role) {
            case '1': // PJ Simpanan
                return redirect()->route('admin.pj_simpanan.home');
            case '2': // Kasie
                return redirect()->route('admin.kasie.home');
            case '3': // Kasir
                return redirect()->route('admin.kasir.home');
            case '4': // AO
                return redirect()->route('admin.ao.home');
            case '5': // PJ Pinjaman
                return redirect()->route('admin.pj_pinjaman.home');
            case '6': // Keanggotaan
                return redirect()->route('admin.keanggotaan.home');
            default:
                return redirect()->route('admin.dashboard');
        }
    }
}
