@extends('admin.layouts')
@section('title', 'Laporan Realisasi')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Laporan Realisasi Pinjaman</h4>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-content">
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>No. Anggota</th><th>Nama</th><th>No. Kredit</th>
                        <th>Produk</th><th>Pokok</th><th>Realisasi</th><th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r['MEMBER_NO'] ?? '-' }}</td>
                        <td>{{ $r['MEMBER_NAME'] ?? '-' }}</td>
                        <td>{{ $r['LOAN_ACCOUNT_NO'] ?? '-' }}</td>
                        <td>{{ $r['LOAN_PRODUCT_ID'] ?? '-' }}</td>
                        <td>{{ number_format($r['LOAN_AMOUNT'] ?? 0, 2) }}</td>
                        <td>{{ number_format($r['AMOUNT'] ?? 0, 2) }}</td>
                        <td>{{ $r['TRANSACTION_DATE'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="center">Belum ada transaksi realisasi.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
