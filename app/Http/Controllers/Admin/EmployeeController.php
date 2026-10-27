<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends BaseAdminController
{
    public function index(Request $request): View
    {
        $search = $request->input('search', '');
        $query = Employee::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('EMPLOYEE_NAME', 'like', "%{$search}%")
                  ->orWhere('EMPLOYEE_NIP', 'like', "%{$search}%")
                  ->orWhere('EMPLOYEE_POSITION', 'like', "%{$search}%");
            });
        }
        $employees = $query->orderBy('EMPLOYEE_NAME')->paginate(20);
        return view('admin.employee.index', compact('employees', 'search'));
    }

    public function create(): View
    {
        return view('admin.employee.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'EMPLOYEE_NAME' => 'required|max:100',
            'EMPLOYEE_NIP'  => 'required|max:50',
            'EMPLOYEE_POSITION' => 'nullable|max:100',
        ]);
        Employee::create([
            'EMPLOYEE_NAME' => $request->input('EMPLOYEE_NAME'),
            'EMPLOYEE_NIP'  => $request->input('EMPLOYEE_NIP'),
            'EMPLOYEE_POSITION' => $request->input('EMPLOYEE_POSITION'),
            'STATUS' => 1,
            'LOGTIME' => now()->format('Y-m-d H:i:s'),
        ]);
        session()->flash('alert-success', 'Karyawan berhasil ditambahkan.');
        return redirect()->route('admin.employee.index');
    }

    public function edit(int $id): View
    {
        $employee = Employee::findOrFail($id);
        return view('admin.employee.edit', compact('employee'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);
        $request->validate([
            'EMPLOYEE_NAME' => 'required|max:100',
            'EMPLOYEE_NIP'  => 'required|max:50',
        ]);
        $employee->update([
            'EMPLOYEE_NAME' => $request->input('EMPLOYEE_NAME'),
            'EMPLOYEE_NIP'  => $request->input('EMPLOYEE_NIP'),
            'EMPLOYEE_POSITION' => $request->input('EMPLOYEE_POSITION'),
            'LOGTIME' => now()->format('Y-m-d H:i:s'),
        ]);
        session()->flash('alert-success', 'Data karyawan berhasil diperbarui.');
        return redirect()->route('admin.employee.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);
        $employee->update(['STATUS' => 0, 'LOGTIME' => now()->format('Y-m-d H:i:s')]);
        session()->flash('alert-success', 'Karyawan berhasil dinonaktifkan.');
        return redirect()->route('admin.employee.index');
    }
}
