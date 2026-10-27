@extends('admin.layouts')
@section('title', 'Biaya Admin - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    @if(session('alert-success'))
        <div class="card green lighten-5"><div class="card-content green-text">{{ session('alert-success') }}</div></div>
    @endif
    <div class="row" style="align-items:center;margin-bottom:1rem;">
        <div class="col s12"><h5 class="blue-text" style="font-size:1.5rem;">Daftar Biaya</h5></div>
        <div class="col s12"><a href="{{ route('admin.fee.create') }}" class="btn blue waves-effect waves-light"><i class="material-icons left">add</i> Tambah Biaya</a></div>
    </div>
    <div class="table-responsive">
        <table class="highlight">
            <thead><tr>
                <th>No</th><th>Nama Biaya</th><th>Tipe</th><th>Jumlah (Rp)</th><th>Tanggal Update</th><th>Actions</th>
            </tr></thead>
            <tbody>
                @forelse($fees as $f)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $f->FEE_NAME }}</td>
                        <td>{{ $f->FEE_TYPE ? $f->FEE_TYPE : '-' }}</td>
                        <td>Rp {{ number_format($f->FEE_AMOUNT, 0, ',', '.') }}</td>
                        <td>{{ $f->LOGTIME ? \Carbon\Carbon::parse($f->LOGTIME)->format('d M Y H:i') : '-' }}</td>
                        <td>
                            <a href="{{ route('admin.fee.edit', $f->FEE_ID) }}" class="btn blue lighten-1 small"><i class="material-icons">edit</i></a>
                            <form method="POST" action="{{ route('admin.fee.destroy', $f->FEE_ID) }}" class="row-form" onsubmit="return confirm('Hapus biaya ini?');">
                                @csrf @method('DELETE')
                                <button class="btn red small"><i class="material-icons">delete</i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="center grey-text">Belum ada data biaya</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($fees, 'links')){{ $fees->links() }}@endif
</div></div>
@stop
