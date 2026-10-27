@extends('admin.layouts')
@section('title', 'Audit Trail - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;margin-bottom:1rem;">Audit Trail</h5>
    <form method="GET" action="{{ route('admin.audit.index') }}">
        <div class="row">
            <div class="col s12 m3"><div class="input-field">
                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                <label>Tanggal Mulai</label>
            </div></div>
            <div class="col s12 m3"><div class="input-field">
                <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                <label>Tanggal Selesai</label>
            </div></div>
            <div class="col s12 m3"><div class="input-field">
                <input type="text" name="USER_ID" value="{{ request('USER_ID') }}">
                <label>User ID</label>
            </div></div>
            <div class="col s12 m3" style="padding-top:2rem;">
                <button type="submit" class="btn blue"><i class="material-icons left">search</i> Filter</button>
                <a href="{{ route('admin.audit.index') }}" class="btn grey">Reset</a>
            </div>
        </div>
    </form>
    <div class="table-responsive">
        <table class="highlight">
            <thead><tr><th>No</th><th>Waktu</th><th>User ID</th><th>Modul</th><th>Aksi</th><th>IP Address</th></tr></thead>
            <tbody>
                @forelse($audits as $a)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $a->CREATED_AT ? \Carbon\Carbon::parse($a->CREATED_AT)->format('d M Y H:i:s') : '-' }}</td>
                        <td>{{ $a->USER_ID ?? '-' }}</td>
                        <td>{{ $a->MODULE_NAME ?? '-' }}</td>
                        <td>{{ $a->ACTION ?? '-' }}</td>
                        <td>{{ $a->IP_ADDRESS ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="center grey-text">Belum ada data audit</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $audits->links() }}
</div></div>
@stop
