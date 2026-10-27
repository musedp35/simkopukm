@extends('admin.layouts')

@section('title', 'Daftar Module - SIMKOP')

@section('admin-content')
    <div class="card">
        <div class="card-content">
            <div class="row">
                <div class="col s12 m8"><h5 class="blue-text" style="font-size:1.5rem;">Daftar Module</h5></div>
                <div class="col s12 m4" style="text-align:right;">
                    <a href="{{ route('admin.module.create') }}" class="btn blue"><i class="material-icons left">add</i> Tambah Module</a>
                </div>
            </div>

            <div class="table-responsive" style="overflow-x:auto;">
                <table class="highlight striped">
                    <thead>
                        <tr><th>ID</th><th>Nama Module</th><th>Submodule</th><th>Keterangan</th><th style="width:160px;">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse($modules as $mod)
                            <tr>
                                <td><strong>{{ $mod->MODULE_ID }}</strong></td>
                                <td>{{ $mod->MODULE_NAME }}</td>
                                <td>{{ $mod->submodules->count() }}</td>
                                <td>{{ $mod->NOTES ?? '-' }}</td>
                                <td>
                                    <div style="display:flex;gap:4px;">
                                        <a href="{{ route('admin.module.edit', $mod->MODULE_ID) }}" class="btn-small orange"><i class="material-icons" style="font-size:16px;">edit</i></a>
                                        <form action="{{ route('admin.module.destroy', $mod->MODULE_ID) }}" method="POST" style="margin:0;" onsubmit="return confirm('Hapus module ini?');">
                                            @csrf @method('DELETE')
                                            <button class="btn-small red" type="submit"><i class="material-icons" style="font-size:16px;">delete</i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center;padding:2rem;" class="grey-text">Belum ada data module.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
