@extends('admin.layouts')
@section('title', 'Menu Kasie - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Menu Kasie — SIMKOP</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>
    <div class="row">
        <div class="col s4"><div class="card blue lighten-1"><div class="card-content white-text"><span class="card-title">Akun Aktif</span><h4>{{ $JML_AKUN_AKTIF ?? 0 }}</h4></div></div></div>
        <div class="col s4"><div class="card teal lighten-1"><div class="card-content white-text"><span class="card-title">Total Saldo</span><h4>Rp {{ number_format($TOTAL_SALDO ?? 0,2,',','.') }}</h4></div></div></div>
        <div class="col s4"><div class="card orange lighten-1"><div class="card-content white-text"><span class="card-title">Belum Approval</span><h4>{{ count($TRANS_BELUM_APPROVAL ?? []) }}</h4></div></div></div>
    </div>
    <h6 style="margin:1rem 0 0.5rem;">Transaksi Belum Approval (20 terakhir)</h6>
    <div class="table-responsive"><table class="highlight">
        <thead><tr><th>No</th><th>Tgl</th><th>Akun</th><th>Nama</th><th>Jenis</th><th>Nominal</th></tr></thead>
        <tbody>
            @forelse($TRANS_BELUM_APPROVAL ?? [] as $r)
                <tr><td>{{ $r['TRANSACTION_ID'] ?? '-' }}</td><td>{{ $r['TRANSACTION_DATE'] ?? '-' }}</td><td>{{ $r['SAVINGS_ACCOUNT_NO'] ?? '-' }}</td><td>{{ $r['MEMBER_NAME'] ?? '-' }}</td><td>{{ $r['TRANSACTION_TYPE'] ?? '-' }}</td><td>Rp {{ number_format($r['AMOUNT'] ?? 0,2,',','.') }}</td></tr>
            @empty
                <tr><td colspan="6" class="center grey-text">Tidak ada transaksi menunggu approval.</td></tr>
            @endforelse
        </tbody>
    </table></div>
</div></div>
@endsection
