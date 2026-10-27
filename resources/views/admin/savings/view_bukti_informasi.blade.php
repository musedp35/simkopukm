@extends('admin.layouts')
@section('title', 'Bukti Informasi - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Bukti Informasi (BI) — {{ $row['TRANSACTION_NO'] ?? '—' }}</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;"><i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali</a>
    @if($row)
        <table class="highlight"><tbody>
            <tr><th>No. Transaksi</th><td>{{ $row['TRANSACTION_NO'] }}</td></tr>
            <tr><th>Tgl</th><td>{{ $row['TRANSACTION_DATE'] }}</td></tr>
            <tr><th>Akun</th><td>{{ $row['SAVINGS_ACCOUNT_NO'] }}</td></tr>
            <tr><th>Nama</th><td>{{ $row['MEMBER_NAME'] ?? '—' }}</td></tr>
            <tr><th>Nominal</th><td>Rp {{ number_format($row['AMOUNT'] ?? 0,2,',','.') }}</td></tr>
            <tr><th>Status</th><td>{{ $row['APPROVAL_STATUS_ID'] ? 'Approved' : 'Pending' }}</td></tr>
        </tbody></table>
    @else
        <p class="grey-text">Data tidak ditemukan.</p>
    @endif
</div></div>
@endsection
