@extends('admin.layouts')
@section('title', 'Cetak Saldo Simpanan Harian - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Saldo Simpanan Harian</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>

    @if(request('idHiddenSimHar'))
    <div class="table-responsive">
        <table class="highlight">
            <thead><tr><th>#</th><th>No. Akun</th><th>Nama</th><th>Saldo</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($rows ?? [] as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['SAVINGS_ACCOUNT_NO'] ?? '-' }}</td>
                        <td>{{ $row['MEMBER_NAME'] ?? '-' }}</td>
                        <td>Rp {{ number_format((float)($row['SAVINGS_BALANCE'] ?? 0), 2) }}</td>
                        <td>{{ $row['ACTIVE_ACCOUNT_ID'] == 1 ? 'Aktif' : 'Nonaktif' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="center grey-text">Tidak ada data saldo harian</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @else
    <form method="GET" action="{{ route('admin.savings.cek_saldo_simhar') }}">
        <div class="row">
            <div class="col s12 m6"><div class="input-field">
                <input type="text" name="idHiddenSimHar" required>
                <label>No. Anggota / No. KTP / No. Akun</label>
            </div></div>
            <div class="col s12 m6"><button type="submit" class="btn blue"><i class="material-icons left">search</i> Cari Saldo</button></div>
        </div>
    </form>
    @endif
</div></div>
@stop
