@extends('admin.layouts')
@section('title', 'Desa/Kelurahan - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <div class="row">
        <div class="col s12 m8"><h5 class="blue-text" style="font-size:1.5rem;">Desa / Kelurahan</h5></div>
        <div class="col s12 m4" style="text-align:right;">
            <a href="{{ route('admin.location.village.create') }}" class="btn blue"><i class="material-icons left">add</i> Tambah</a>
        </div>
    </div>
    <div class="table-responsive" style="overflow-x:auto;">
        <table class="highlight striped">
            <thead><tr><th>ID</th><th>Nama Desa</th><th>Kecamatan</th><th style="width:160px;">Aksi</th></tr></thead>
            <tbody>
                @forelse($villages as $v)
                <tr>
                    <td><strong>{{ $v->VILLAGE_ID }}</strong></td>
                    <td>{{ $v->VILLAGE_NAME }}</td>
                    <td>{{ $v->district->DISTRICT_NAME ?? '-' }}</td>
                    <td>
                        <div style="display:flex;gap:4px;">
                            <a href="{{ route('admin.location.village.edit', $v->VILLAGE_ID) }}" class="btn-small orange"><i class="material-icons" style="font-size:16px;">edit</i></a>
                            <form action="{{ route('admin.location.village.destroy', $v->VILLAGE_ID) }}" method="POST" style="margin:0;" onsubmit="return confirm('Hapus?');">
                                @csrf @method('DELETE')
                                <button class="btn-small red" type="submit"><i class="material-icons" style="font-size:16px;">delete</i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;padding:2rem;" class="grey-text">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $villages->links() }}
</div></div>
@stop
