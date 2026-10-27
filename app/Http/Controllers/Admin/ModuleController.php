<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Module;
use App\Models\Submodule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModuleController extends BaseAdminController
{
    public function index(): View
    {
        $modules = Module::with('submodules')->get();
        return view('admin.module.index', compact('modules'));
    }

    public function create(): View
    {
        return view('admin.module.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'MODULE_NAME' => 'required|max:100',
            'NOTES' => 'nullable',
        ]);

        $moduleId = strtoupper(substr(md5(uniqid()), 0, 4));
        Module::create([
            'MODULE_ID' => $moduleId,
            'MODULE_NAME' => $request->input('MODULE_NAME'),
            'NOTES' => $request->input('NOTES'),
            'UPDATE_BY' => $this->userName,
            'LOGTIME' => now()->format('Y-m-d H:i:s'),
        ]);

        session()->flash('alert-success', 'Module berhasil ditambahkan.');
        return redirect()->route('admin.module.index');
    }

    public function edit(string $moduleId): View
    {
        $module = Module::find($moduleId);
        $submodules = Submodule::where('MODULE_ID', $moduleId)->get();
        return view('admin.module.edit', compact('module', 'submodules'));
    }

    public function update(Request $request, string $moduleId): RedirectResponse
    {
        $module = Module::find($moduleId);
        $request->validate([
            'MODULE_NAME' => 'required|max:100',
            'NOTES' => 'nullable',
        ]);

        $module->update([
            'MODULE_NAME' => $request->input('MODULE_NAME'),
            'NOTES' => $request->input('NOTES'),
            'UPDATE_BY' => $this->userName,
            'LOGTIME' => now()->format('Y-m-d H:i:s'),
        ]);

        session()->flash('alert-success', 'Module berhasil diperbarui.');
        return redirect()->route('admin.module.index');
    }

    public function destroy(string $moduleId): RedirectResponse
    {
        Module::where('MODULE_ID', $moduleId)->delete();
        session()->flash('alert-success', 'Module berhasil dihapus.');
        return redirect()->route('admin.module.index');
    }
}
