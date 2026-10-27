@extends('admin.layouts')
@section('title', 'Tagihan Jasa SSB - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Tagihan Jasa SSB (Jatuh Tempo)</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>

    <div class="table-responsive">
        <table class="highlight">
            <thead><tr><th>#</th><th>No. Akun</th><th>Nama Anggota</th><th>Rek. Bank</th><th>Saldo</th></tr></thead>
            <tbody>
                @forelse($rows ?? [] as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['SAVINGS_ACCOUNT_NO'] ?? '-' }}</td>
                        <td>{{ $row['MEMBER_NAME'] ?? '-' }}</td>
                        <td>{{ $row['BANKING_ACCOUNT_NO'] ?? '-' }}</td>
                        <td>Rp {{ number_format((float)($row['SAVINGS_BALANCE'] ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="center grey-text">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@stop
