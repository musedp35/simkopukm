@extends('admin.layouts')

@section('title', 'Daftar Role - SIMKOP')

@section('admin-content')
    <div class="card">
        <div class="card-content">
            <div class="row">
                <div class="col s12 m8"><h5 class="blue-text" style="font-size:1.5rem;">Daftar Role</h5></div>
                <div class="col s12 m4" style="text-align:right;">
                    <a href="{{ route('admin.role.create') }}" class="btn blue"><i class="material-icons left">add</i> Tambah Role</a>
                </div>
            </div>

            <div class="table-responsive" style="overflow-x:auto;">
                <table class="highlight striped">
                    <thead>
                        <tr><th>ID</th><th>Nama Role</th><th>Module</th><th>Keterangan</th><th>Update Oleh</th><th style="width:160px;">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $r)
                            <tr>
                                <td><strong>{{ $r->ROLE_ID }}</strong></td>
                                <td>{{ $r->ROLE_NAME }}</td>
                                <td>{{ $r->module->MODULE_NAME ?? '-' }}</td>
                                <td>{{ $r->NOTES ?? '-' }}</td>
                                <td>{{ $r->UPDATE_BY ?? '-' }}</td>
                                <td>
                                    <div style="display:flex;gap:4px;">
                                        <a href="{{ route('admin.role.edit', $r->ROLE_ID) }}" class="btn-small orange"><i class="material-icons" style="font-size:16px;">edit</i></a>
                                        <form action="{{ route('admin.role.destroy', $r->ROLE_ID) }}" method="POST" style="margin:0;" onsubmit="return confirm('Hapus role ini?');">
                                            @csrf @method('DELETE')
                                            <button class="btn-small red" type="submit"><i class="material-icons" style="font-size:16px;">delete</i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" style="text-align:center;padding:2rem;" class="grey-text">Belum ada data role.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
