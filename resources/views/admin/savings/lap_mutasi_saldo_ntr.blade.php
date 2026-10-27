@extends('admin.layouts')
@section('title', 'Lap. Mutasi Saldo NTR - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Mutasi Saldo NTR</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;"><i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali</a>
    <form method="GET" action="{{ route('admin.savings.lap_mutasi_saldo_ntr') }}">
        <div class="row">
            <div class="col s4 m3"><div class="input-field"><input type="date" name="dtpTgl" value="{{ request('dtpTgl') }}"><label>Tgl Awal</label></div></div>
            <div class="col s4 m3"><div class="input-field"><input type="date" name="dtpTglAkhir" value="{{ request('dtpTglAkhir') }}"><label>Tgl Akhir</label></div></div>
            <div class="col s4 m3"><button type="submit" class="btn blue"><i class="material-icons left">search</i> Cari</button></div>
        </div>
    </form>

    <h6 class="blue-text">Rekap Setor</h6>
    <div class="table-responsive">
        <table class="highlight">
            <thead><tr><th>#</th><th>No. Anggota</th><th>Nama</th><th>Total Setor</th></tr></thead>
            <tbody>
                @forelse($rekapSetor ?? [] as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['MEMBER_NO'] ?? '-' }}</td>
                        <td>{{ $row['MEMBER_NAME'] ?? '-' }}</td>
                        <td>Rp {{ number_format((float)($row['TOTAL_SETOR'] ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="center grey-text">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h6 class="blue-text">Rekap Tarik</h6>
    <div class="table-responsive">
        <table class="highlight">
            <thead><tr><th>#</th><th>No. Anggota</th><th>Nama</th><th>Total Tarik</th></tr></thead>
            <tbody>
                @forelse($rekapTarik ?? [] as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['MEMBER_NO'] ?? '-' }}</td>
                        <td>{{ $row['MEMBER_NAME'] ?? '-' }}</td>
                        <td>Rp {{ number_format((float)($row['TOTAL_TARIK'] ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="center grey-text">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@stop
