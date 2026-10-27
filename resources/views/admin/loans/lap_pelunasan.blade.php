@extends('admin.layouts')
@section('title', 'Laporan Pelunasan')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Laporan Pelunasan Pinjaman</h4>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-content">
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>No. Anggota</th><th>Nama</th><th>No. Kredit</th>
                        <th>Pokok</th><th>Tanggal Pinjam</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r['MEMBER_NO'] ?? '-' }}</td>
                        <td>{{ $r['MEMBER_NAME'] ?? '-' }}</td>
                        <td>{{ $r['LOAN_ACCOUNT_NO'] ?? '-' }}</td>
                        <td>{{ number_format($r['LOAN_AMOUNT'] ?? 0, 2) }}</td>
                        <td>{{ $r['LOAN_DATE'] ?? '-' }}</td>
                        <td><span class="green-text">Lunas</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="center">Belum ada pinjaman yang pelunasan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
