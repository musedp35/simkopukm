@extends('admin.layouts')
@section('title', 'Daftar Akun SSB - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Daftar Akun SSB</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;"><i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali</a>
    <form method="GET" action="{{ route('admin.savings.lap_daftar_akun_ssb') }}">
        <div class="row">
            <div class="col s4 m3"><div class="input-field"><input type="date" name="dtpTgl" value="{{ request('dtpTgl') }}"><label>Tgl Awal</label></div></div>
            <div class="col s4 m3"><div class="input-field"><input type="date" name="dtpTglAkhir" value="{{ request('dtpTglAkhir') }}"><label>Tgl Akhir</label></div></div>
            <div class="col s4 m3"><div class="input-field">
                <select name="cmbStatus">
                    <option value="0" @selected(request('cmbStatus',0)==0)>Semua</option>
                    <option value="4" @selected(request('cmbStatus')==4)>NTR</option>
                    <option value="10" @selected(request('cmbStatus')==10)>TR</option>
                </select><label>Status Anggota</label>
            </div></div>
            <div class="col s4 m3"><button type="submit" class="btn blue"><i class="material-icons left">search</i> Cari</button></div>
        </div>
    </form>

    @if(!empty($rekap))
    <div class="row">
        <div class="col s4 m4"><div class="card blue lighten-4"><div class="card-content white-text"><span class="card-title">Jumlah Akun</span><h3>{{ $rekap['JML_AKUN'] ?? 0 }}</h3></div></div></div>
        <div class="col s4 m4"><div class="card green lighten-4"><div class="card-content white-text"><span class="card-title">Total Saldo</span><h3>Rp {{ number_format((float)($rekap['TOTAL_SALDO'] ?? 0), 2) }}</h3></div></div></div>
    </div>
    @endif

    <div class="table-responsive">
        <table class="highlight">
            <thead><tr><th>#</th><th>No. Akun</th><th>No. Anggota</th><th>Nama</th><th>Saldo</th><th>Tgl. Buka</th></tr></thead>
            <tbody>
                @forelse($rows ?? [] as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['SAVINGS_ACCOUNT_NO'] ?? '-' }}</td>
                        <td>{{ $row['MEMBER_NO'] ?? '-' }}</td>
                        <td>{{ $row['MEMBER_NAME'] ?? '-' }}</td>
                        <td>Rp {{ number_format((float)($row['SAVINGS_BALANCE'] ?? 0), 2) }}</td>
                        <td>{{ $row['LOGTIME'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="center grey-text">Tidak ada akun SSB</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@stop
