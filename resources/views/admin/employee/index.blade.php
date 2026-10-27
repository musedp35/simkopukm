@extends('admin.layouts')
@section('title', 'Daftar Karyawan - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    @if(session('alert-success'))
        <div class="card green lighten-5"><div class="card-content green-text">{{ session('alert-success') }}</div></div>
    @endif
    <div class="row" style="align-items:center;margin-bottom:1rem;">
        <div class="col s12"><h5 class="blue-text" style="font-size:1.5rem;">Daftar Karyawan</h5></div>
        <div class="col s12"><a href="{{ route('admin.employee.create') }}" class="btn blue waves-effect waves-light"><i class="material-icons left">add</i> Tambah Karyawan</a></div>
    </div>
    <div class="table-responsive">
        <table class="highlight">
            <thead><tr>
                <th>No</th><th>Nama</th><th>NIP</th><th>Posisi</th><th>Status</th><th>Actions</th>
            </tr></thead>
            <tbody>
                @forelse($employees as $e)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $e->EMPLOYEE_NAME }}</td>
                        <td>{{ $e->EMPLOYEE_NIP }}</td>
                        <td>{{ $e->EMPLOYEE_POSITION }}</td>
                        <td>
                            @if($e->STATUS == 1)
                                <span class="chip green white-text">Aktif</span>
                            @else
                                <span class="chip grey">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.employee.edit', $e->EMPLOYEE_ID) }}" class="btn blue lighten-1 small"><i class="material-icons">edit</i></a>
                            <form method="POST" action="{{ route('admin.employee.destroy', $e->EMPLOYEE_ID) }}" class="row-form" onsubmit="return confirm('Nonaktifkan Karyawan ini?');">
                                @csrf @method('DELETE')
                                <button class="btn red small"><i class="material-icons">delete</i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="center grey-text">Belum ada data karyawan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($employees, 'links')){{ $employees->links() }}@endif
</div></div>
@stop
