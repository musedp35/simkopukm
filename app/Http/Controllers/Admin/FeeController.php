<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Fee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeeController extends BaseAdminController
{
    public function index(): View
    {
        $fees = Fee::orderBy('FEE_NAME')->paginate(20);
        return view('admin.fee.index', compact('fees'));
    }

    public function create(): View
    {
        return view('admin.fee.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'FEE_NAME' => 'required|max:100',
            'FEE_AMOUNT' => 'required|numeric|min:0',
            'FEE_TYPE' => 'nullable|max:50',
        ]);
        Fee::create([
            'FEE_NAME' => $request->input('FEE_NAME'),
            'FEE_AMOUNT' => $request->input('FEE_AMOUNT'),
            'FEE_TYPE' => $request->input('FEE_TYPE'),
            'LOGTIME' => now()->format('Y-m-d H:i:s'),
        ]);
        session()->flash('alert-success', 'Biaya (fee) berhasil ditambahkan.');
        return redirect()->route('admin.fee.index');
    }

    public function edit(int $id): View
    {
        $fee = Fee::findOrFail($id);
        return view('admin.fee.edit', compact('fee'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $fee = Fee::findOrFail($id);
        $request->validate([
            'FEE_NAME' => 'required|max:100',
            'FEE_AMOUNT' => 'required|numeric|min:0',
        ]);
        $fee->update([
            'FEE_NAME' => $request->input('FEE_NAME'),
            'FEE_AMOUNT' => $request->input('FEE_AMOUNT'),
            'FEE_TYPE' => $request->input('FEE_TYPE'),
            'LOGTIME' => now()->format('Y-m-d H:i:s'),
        ]);
        session()->flash('alert-success', 'Biaya (fee) berhasil diperbarui.');
        return redirect()->route('admin.fee.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        Fee::where('FEE_ID', $id)->delete();
        session()->flash('alert-success', 'Biaya (fee) berhasil dihapus.');
        return redirect()->route('admin.fee.index');
    }
}
