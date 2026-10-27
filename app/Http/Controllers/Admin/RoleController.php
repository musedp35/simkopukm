<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Role;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends BaseAdminController
{
    public function index(): View
    {
        $roles = Role::with('module')->get();
        $modules = Module::all();
        return view('admin.role.index', compact('roles', 'modules'));
    }

    public function create(): View
    {
        $modules = Module::all();
        return view('admin.role.create', compact('modules'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'ROLE_NAME' => 'required|max:100',
            'NOTES' => 'nullable',
            'MODULE_ID' => 'nullable',
        ]);

        $roleId = strtoupper(substr(md5(uniqid()), 0, 4));
        Role::create([
            'ROLE_ID' => $roleId,
            'ROLE_NAME' => $request->input('ROLE_NAME'),
            'NOTES' => $request->input('NOTES'),
            'MODULE_ID' => $request->input('MODULE_ID'),
            'UPDATE_BY' => $this->userName,
            'LOGTIME' => now()->format('Y-m-d H:i:s'),
        ]);

        session()->flash('alert-success', 'Role berhasil ditambahkan.');
        return redirect()->route('admin.role.index');
    }

    public function edit(string $roleId): View
    {
        $role = Role::find($roleId);
        $modules = Module::all();
        return view('admin.role.edit', compact('role', 'modules'));
    }

    public function update(Request $request, string $roleId): RedirectResponse
    {
        $role = Role::find($roleId);
        $request->validate([
            'ROLE_NAME' => 'required|max:100',
            'NOTES' => 'nullable',
            'MODULE_ID' => 'nullable',
        ]);

        $role->update([
            'ROLE_NAME' => $request->input('ROLE_NAME'),
            'NOTES' => $request->input('NOTES'),
            'MODULE_ID' => $request->input('MODULE_ID'),
            'UPDATE_BY' => $this->userName,
            'LOGTIME' => now()->format('Y-m-d H:i:s'),
        ]);

        session()->flash('alert-success', 'Role berhasil diperbarui.');
        return redirect()->route('admin.role.index');
    }

    public function destroy(string $roleId): RedirectResponse
    {
        Role::where('ROLE_ID', $roleId)->delete();
        session()->flash('alert-success', 'Role berhasil dihapus.');
        return redirect()->route('admin.role.index');
    }
}
