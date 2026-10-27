@extends('admin.layouts')
@section('title', 'Daftar Akun Simpanan - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Daftar Pembukaan Akun Simpanan</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>

    <div class="table-responsive">
        <table class="highlight">
            <thead><tr><th>#</th><th>Tgl Buka</th><th>No. Akun</th><th>Nama Anggota</th><th>No. Bukti</th></tr></thead>
            <tbody>
                @forelse($rows ?? [] as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ date('d/m/Y', strtotime($row['DATE_REGISTRATION'] ?? $row['LOGTIME'] ?? '')) }}</td>
                        <td>{{ $row['SAVINGS_ACCOUNT_NO'] ?? '-' }}</td>
                        <td>{{ $row['MEMBER_NAME'] ?? $row['SAVINGS_ACCOUNT_NAME'] ?? '-' }}</td>
                        <td>{{ $row['MEMBER_NO'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="center grey-text">Belum ada akun simpanan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div></div>
@stop
