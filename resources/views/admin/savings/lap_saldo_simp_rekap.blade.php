@extends('admin.layouts')
@section('title', 'Lap. Saldo Simpanan Rekap - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Rekap Saldo Simpanan Harian</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;"><i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali</a>
    <form method="GET" action="{{ route('admin.savings.lap_saldo_simp_rekap') }}">
        <div class="row">
            <div class="col s4 m3"><div class="input-field"><input type="date" name="dtpTgl" value="{{ request('dtpTgl') }}"><label>Tgl Awal</label></div></div>
            <div class="col s4 m3"><div class="input-field"><input type="date" name="dtpTglAkhir" value="{{ request('dtpTglAkhir') }}"><label>Tgl Akhir</label></div></div>
            <div class="col s4 m3"><div class="input-field"><select class="browser-default" name="cmbStatus"><option value="0">Semua</option><option value="1">TR</option><option value="2">NTR</option></select><label>Status</label></div></div>
            <div class="col s12 m3"><button type="submit" class="btn blue"><i class="material-icons left">search</i> Cari</button></div>
        </div>
    </form>
    <div class="table-responsive">
        <table class="highlight">
            <thead><tr><th>Tgl</th><th>Total Mutasi</th><th>Jumlah Transaksi</th></tr></thead>
            <tbody>
                @forelse($rows ?? [] as $row)
                    <tr>
                        <td>{{ $row['TRANSACTION_DATE'] ?? '-' }}</td>
                        <td>Rp {{ number_format((float)($row['TOTAL_AMOUNT'] ?? 0), 2) }}</td>
                        <td>{{ $row['JML_TRANS'] ?? 0 }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="center grey-text">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@stop
