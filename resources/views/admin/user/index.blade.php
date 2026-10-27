@extends('admin.layouts')
@section('title', 'Akun Login - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    @if(session('alert-success'))
        <div class="card green lighten-5"><div class="card-content green-text">{{ session('alert-success') }}</div></div>
    @endif
    <div class="row" style="align-items:center;margin-bottom:1rem;">
        <div class="col s12"><h5 class="blue-text" style="font-size:1.5rem;">Manajemen Akun Login</h5></div>
        <div class="col s12"><a href="{{ route('admin.user.create') }}" class="btn blue waves-effect waves-light"><i class="material-icons left">add</i> Buat Akun</a></div>
    </div>
    <div class="table-responsive">
        <table class="highlight">
            <thead><tr>
                <th>No</th><th>Username</th><th>Nama Member</th><th>Role</th><th>Status</th><th>Terakhir Masuk</th><th>Actions</th>
            </tr></thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $u->USERNAME }}</td>
                        <td>{{ $u->MEMBER_NAME ?? '-' }}</td>
                        <td>
                            @if($u->ROLE_NAME)
                                <span class="chip {{ $u->ROLE_NAME === 'Admin' ? 'orange' : 'blue' }} white-text">{{ $u->ROLE_NAME }}</span>
                            @else
                                <span class="chip grey">-</span>
                            @endif
                        </td>
                        <td>
                            @if($u->USER_STATUS == 1)
                                <span class="chip green white-text">Aktif</span>
                            @else
                                <span class="chip grey">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            @if($u->last_login)
                                {{ \Carbon\Carbon::parse($u->last_login)->format('d M Y H:i') }}
                            @else
                                <span class="grey-text">Belum pernah masuk</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap;">
                            <a href="{{ route('admin.user.edit', $u->MEMBER_REGISTRY) }}" class="btn blue lighten-1 small"><i class="material-icons">edit</i></a>
                            <form method="POST" action="{{ route('admin.user.destroy', $u->MEMBER_REGISTRY) }}" class="row-form" onsubmit="return confirm('Hapus akun ini?');">
                                @csrf @method('DELETE')
                                <button class="btn red small"><i class="material-icons">delete</i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="center grey-text">Belum ada akun login</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($users, 'links')){{ $users->links() }}@endif
</div></div>
@stop
