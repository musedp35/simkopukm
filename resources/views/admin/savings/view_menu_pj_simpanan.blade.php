@extends('admin.layouts')
@section('title', 'Menu PJ Simpanan - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Menu PJ Simpanan — Menunggu Approval</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;"><i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali</a>
    <div class="table-responsive"><table class="highlight">
        <thead><tr><th>No</th><th>No. Transaksi</th><th>Tgl</th><th>Akun</th><th>Nama</th><th>Nominal</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($rows ?? [] as $r)
                <tr><td>{{ $r['TRANSACTION_ID'] }}</td><td>{{ $r['TRANSACTION_NO'] }}</td><td>{{ $r['TRANSACTION_DATE'] }}</td><td>{{ $r['SAVINGS_ACCOUNT_NO'] }}</td><td>{{ $r['MEMBER_NAME'] }}</td><td>Rp {{ number_format($r['AMOUNT'] ?? 0,2,',','.') }}</td><td>{{ $r['APPROVAL_STATUS_ID'] ? 'Approved' : 'Pending' }}</td></tr>
            @empty <tr><td colspan="7" class="center grey-text">Tidak ada transaksi menunggu.</td></tr> @endforelse
        </tbody>
    </table></div>
</div></div>
@endsection
