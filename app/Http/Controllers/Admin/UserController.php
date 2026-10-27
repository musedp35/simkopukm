<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\User;
use App\Models\Role;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends BaseAdminController
{
    public function index(Request $request): View
    {
        $search = $request->input('search', '');
        $query = DB::table('ma_user as u')
            ->leftJoin('ma_role as r', 'r.ROLE_ID', '=', 'u.ROLE_ID')
            ->leftJoin('ma_member as m', 'm.MEMBER_REGISTRY', '=', 'u.MEMBER_REGISTRY')
            ->select('u.MEMBER_REGISTRY', 'u.USERNAME', 'u.ROLE_ID', 'u.USER_STATUS',
                     'u.last_login', 'r.ROLE_NAME', 'm.MEMBER_NAME');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('u.USERNAME', 'like', "%{$search}%")
                  ->orWhere('m.MEMBER_NAME', 'like', "%{$search}%");
            });
        }
        $users = $query->orderBy('u.USERNAME')->paginate(20);
        $roles = Role::all();
        $members = Member::where('MEMBER_NO', '!=', '')->get();
        return view('admin.user.index', compact('users', 'roles', 'members', 'search'));
    }

    public function create(): View
    {
        $roles = Role::all();
        $members = Member::where('MEMBER_NO', '!=', '')->get();
        return view('admin.user.create', compact('roles', 'members'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'MEMBER_REGISTRY' => 'required',
            'USERNAME' => 'required|max:100',
            'ROLE_ID' => 'required',
            'PASSWORD' => 'required|min:6',
        ]);
        User::create([
            'MEMBER_REGISTRY' => $request->input('MEMBER_REGISTRY'),
            'ROLE_ID' => $request->input('ROLE_ID'),
            'USERNAME' => $request->input('USERNAME'),
            'PASSWORD' => Hash::make($request->input('PASSWORD')),
            'USER_STATUS' => 1,
            'UPDATE_BY' => $this->userName,
            'LOGTIME' => now()->format('Y-m-d H:i:s'),
        ]);
        session()->flash('alert-success', 'User berhasil ditambahkan.');
        return redirect()->route('admin.user.index');
    }

    public function edit(string $registry): View
    {
        $user = User::where('MEMBER_REGISTRY', $registry)->firstOrFail();
        $roles = Role::all();
        return view('admin.user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, string $registry): RedirectResponse
    {
        $user = User::where('MEMBER_REGISTRY', $registry)->firstOrFail();
        $request->validate([
            'USERNAME' => 'required|max:100',
            'ROLE_ID' => 'required',
            'PASSWORD' => 'nullable|min:6',
        ]);
        $data = [
            'USERNAME' => $request->input('USERNAME'),
            'ROLE_ID' => $request->input('ROLE_ID'),
            'UPDATE_BY' => $this->userName,
            'LOGTIME' => now()->format('Y-m-d H:i:s'),
        ];
        if ($request->input('PASSWORD')) {
            $data['PASSWORD'] = Hash::make($request->input('PASSWORD'));
        }
        $user->update($data);
        session()->flash('alert-success', 'User berhasil diperbarui.');
        return redirect()->route('admin.user.index');
    }

    public function destroy(string $registry): RedirectResponse
    {
        User::where('MEMBER_REGISTRY', $registry)->delete();
        session()->flash('alert-success', 'User berhasil dihapus.');
        return redirect()->route('admin.user.index');
    }
}
