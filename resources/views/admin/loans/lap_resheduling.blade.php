@extends('admin.layouts')
@section('title', 'Laporan Resheduling')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Laporan Resheduling</h4>
    <div class="card">
        <div class="card-content">
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>No. Anggota</th><th>Nama</th><th>No. Kredit</th>
                        <th>Pokok</th><th>Sisa</th><th>Tanggal Pinjam</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r['MEMBER_NO'] ?? '-' }}</td>
                        <td>{{ $r['MEMBER_NAME'] ?? '-' }}</td>
                        <td>{{ $r['LOAN_ACCOUNT_NO'] ?? '-' }}</td>
                        <td>{{ number_format($r['LOAN_AMOUNT'] ?? 0, 2) }}</td>
                        <td>{{ number_format($r['REMAINING_AMOUNT'] ?? 0, 2) }}</td>
                        <td>{{ $r['LOAN_DATE'] ?? '-' }}</td>
                        <td><span class="orange-text">Resheduling</span></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="center">Belum ada pinjaman dalam kondisi resheduling.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
