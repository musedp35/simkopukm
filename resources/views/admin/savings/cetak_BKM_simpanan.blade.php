@extends('admin.layouts')
@section('title', 'Cetak BKM Simpanan - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Bukti Kas Masuk (BKM) Simpanan</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>

    @if($row ?? null)
    <div class="table-responsive">
        <table class="highlight">
            <tbody>
                <tr><th>No. Transaksi</th><td>{{ $row['TRANSACTION_NO'] ?? $row['TRANSACTION_ID'] ?? '-' }}</td></tr>
                <tr><th>Tanggal</th><td>{{ $row['TRANSACTION_DATE'] ?? '-' }}</td></tr>
                <tr><th>Nama Penerima</th><td>{{ $row['MEMBER_NAME'] ?? '-' }}</td></tr>
                <tr><th>Jenis</th><td>{{ $row['TRANSACTION_TYPE'] ?? '-' }}</td></tr>
                <tr><th>No. Akun</th><td>{{ $row['SAVINGS_ACCOUNT_NO'] ?? '-' }}</td></tr>
                <tr><th>Nominal</th><td>Rp {{ number_format((float)($row['AMOUNT'] ?? 0), 2) }}</td></tr>
            </tbody>
        </table>
    </div>
    @else
    <p class="grey-text">Tidak ada data BKM untuk transaksi ini.</p>
    @endif
</div></div>
@stop
