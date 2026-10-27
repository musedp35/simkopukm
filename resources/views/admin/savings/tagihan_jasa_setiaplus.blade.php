@extends('admin.layouts')
@section('title', 'Tagihan Jasa SetiaPlus - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Tagihan Jasa SetiaPlus (Jatuh Tempo)</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>

    @if(session('alert-success'))
        <div class="card green lighten-5"><div class="card-content green-text">{{ session('alert-success') }}</div></div>
    @endif

    <div class="table-responsive">
        <table class="highlight">
            <thead><tr><th>#</th><th>No. Akun</th><th>Nama Anggota</th><th>Rek. Bank</th><th>Saldo</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($rows ?? [] as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['SAVINGS_ACCOUNT_NO'] ?? '-' }}</td>
                        <td>{{ $row['MEMBER_NAME'] ?? '-' }}</td>
                        <td>{{ $row['BANKING_ACCOUNT_NO'] ?? '-' }}</td>
                        <td>Rp {{ number_format((float)($row['SAVINGS_BALANCE'] ?? 0), 2) }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.savings.proses_bayar_jasa_setiaplus') }}" class="row-form">
                                @csrf
                                <input type="hidden" name="edtNoacc" value="{{ $row['SAVINGS_ACCOUNT_NO'] ?? '' }}">
                                <button type="submit" class="btn small blue"><i class="material-icons left">payments</i> Bayar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="center grey-text">Tidak ada tagihan jatuh tempo</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@stop
