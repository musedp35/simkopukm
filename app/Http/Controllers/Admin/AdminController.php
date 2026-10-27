<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Member;

/**
 * AdminController - Dashboard
 * 
 * Pengganti controller CI3: application/controllers/Admin.php
 * 
 * Konvensi mengikuti rules.md:
 * - Nama file: UpperCamelCase
 * - Method: camelCase
 */
class AdminController extends BaseAdminController
{
    /**
     * Dashboard per-role
     * Pengganti: Admin::index() di CI3
     */
    public function dashboard()
    {
        $this->refreshUser();
        $role = $this->userLevel;
        $roleName = $this->user->ROLE_NAME ?? 'Admin';

        // Dashboard per-role (menggantikan switch-case di CI3)
        $data = [
            'user' => $this->user,
            'role' => $this->user->role ?? null,
            'roleName' => $roleName,
        ];

        // ROLE_ID di DB ini memakai kode huruf (A=Admin, 1..6 = level fungsional)
        switch ($role) {
            case '1': // PJ Simpanan
                $data['detailTotAgtDashboard'] = Member::getTotAgtDashboard();
                return $this->view('admin.dashboards.pj_simpanan', $data);
            case '2': // Kasie
                return $this->view('admin.dashboards.kasie', $data);
            case '3': // Kasir
                return $this->view('admin.dashboards.kasir', $data);
            case '4': // AO
                return $this->view('admin.dashboards.ao', $data);
            case '5': // PJ Pinjaman
                return $this->view('admin.dashboards.pj_pinjaman', $data);
            case '6': // Keanggotaan
                $data['detailTotAgtDashboard'] = Member::getTotAgtDashboard();
                $data['detailTotNewAgtDashboard'] = Member::getTotNewAgtDashboard();
                $data['detailTotCalonAgtDashboard'] = Member::getTotCalonAgtDashboard();
                return $this->view('admin.dashboards.keanggotaan', $data);
            default:
                // Role 'A' (Admin full access) atau role tak dikenal → dashboard default
                $data['detailTotAgtDashboard'] = Member::getTotAgtDashboard();
                return $this->view('admin.dashboards.default', $data);
        }
    }
}
