@extends('admin.layouts')
@section('title', 'Kota/Kabupaten - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <div class="row">
        <div class="col s12 m8"><h5 class="blue-text" style="font-size:1.5rem;">Kota / Kabupaten</h5></div>
        <div class="col s12 m4" style="text-align:right;">
            <a href="{{ route('admin.location.city.create') }}" class="btn blue"><i class="material-icons left">add</i> Tambah</a>
        </div>
    </div>
    <div class="table-responsive" style="overflow-x:auto;">
        <table class="highlight striped">
            <thead><tr><th>ID</th><th>Nama</th><th>Tipe</th><th style="width:160px;">Aksi</th></tr></thead>
            <tbody>
                @forelse($cities as $c)
                <tr>
                    <td><strong>{{ $c->CITY_ID }}</strong></td>
                    <td>{{ $c->CITY_NAME }}</td>
                    <td>{{ $c->typeCity->TYPE_CITY_NAME ?? '-' }}</td>
                    <td>
                        <div style="display:flex;gap:4px;">
                            <a href="{{ route('admin.location.city.edit', $c->CITY_ID) }}" class="btn-small orange"><i class="material-icons" style="font-size:16px;">edit</i></a>
                            <form action="{{ route('admin.location.city.destroy', $c->CITY_ID) }}" method="POST" style="margin:0;" onsubmit="return confirm('Hapus?');">
                                @csrf @method('DELETE')
                                <button class="btn-small red" type="submit"><i class="material-icons" style="font-size:16px;">delete</i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="grey-text" style="text-align:center;padding:2rem;">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $cities->links() }}
</div></div>
@stop
