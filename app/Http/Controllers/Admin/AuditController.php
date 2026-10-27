<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends BaseAdminController
{
    public function index(Request $request): View
    {
        $query = Audit::query();

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('CREATED_AT', '>=', $request->input('tanggal_mulai'));
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('CREATED_AT', '<=', $request->input('tanggal_selesai'));
        }

        // Filter berdasarkan user
        if ($request->filled('USER_ID')) {
            $query->where('USER_ID', $request->input('USER_ID'));
        }

        $audits = $query->orderBy('CREATED_AT', 'desc')->paginate(50);

        return view('admin.audit.index', compact('audits'));
    }
}
