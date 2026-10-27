@extends('admin.layouts')
@section('title', 'Cetak BU Simpanan - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Bukti Urutan (BU) Simpanan</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>

    @if($row ?? null)
    <div class="table-responsive">
        <table class="highlight">
            <tbody>
                <tr><th>No. Transaksi</th><td>{{ $row['TRANSACTION_NO'] ?? '-' }}</td></tr>
                <tr><th>Tanggal</th><td>{{ $row['TRANSACTION_DATE'] ?? '-' }}</td></tr>
                <tr><th>No. Akun</th><td>{{ $row['SAVINGS_ACCOUNT_NO'] ?? '-' }}</td></tr>
                <tr><th>Jenis</th><td>{{ $row['TRANSACTION_TYPE'] ?? '-' }}</td></tr>
                <tr><th>Jumlah</th><td>Rp {{ number_format((float)($row['AMOUNT'] ?? 0), 2) }}</td></tr>
                <tr><th>Status</th><td>{{ $row['APPROVAL_STATUS_ID'] == 1 ? 'Disetujui' : ($row['APPROVAL_STATUS_ID'] == 0 ? 'Menunggu' : 'Ditolak') }}</td></tr>
            </tbody>
        </table>
    </div>
    @else
    <p class="grey-text">Tidak ada data BU untuk transaksi ini.</p>
    @endif
</div></div>
@stop
