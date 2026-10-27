@extends('admin.layouts')
@section('title', 'Laporan Pembukaan Akun NTR - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Laporan Pembukaan Akun NTR</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>

    <form method="GET" action="{{ route('admin.savings.lap_pembukaan_akun_ntr') }}">
        <div class="row">
            <div class="col s6 m3"><div class="input-field">
                <input type="date" name="dtpTgl" value="{{ request('dtpTgl') }}">
                <label>Tanggal Awal</label>
            </div></div>
            <div class="col s6 m3"><div class="input-field">
                <input type="date" name="dtpTglAkhir" value="{{ request('dtpTglAkhir') }}">
                <label>Tanggal Akhir</label>
            </div></div>
            <div class="col s6 m3"><button type="submit" class="btn blue"><i class="material-icons left">search</i> Filter</button></div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="highlight">
            <thead><tr><th>#</th><th>Tgl Buka</th><th>No. Akun</th><th>Nama Anggota</th><th>No. Anggota</th></tr></thead>
            <tbody>
                @forelse($rows ?? [] as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ date('d/m/Y', strtotime($row['LOGTIME'] ?? '')) }}</td>
                        <td>{{ $row['SAVINGS_ACCOUNT_NO'] }}</td>
                        <td>{{ $row['MEMBER_NAME'] }}</td>
                        <td>{{ $row['MEMBER_NO'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="center grey-text">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@stop
