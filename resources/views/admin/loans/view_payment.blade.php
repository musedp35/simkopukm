@extends('admin.layouts')
@section('title', 'Pembayaran Pinjaman')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Pembayaran Pinjaman Hari Ini</h4>
    <div class="card">
        <div class="card-content">
            <h5 style="font-size:1rem; color:#555">Realisasi (Pencairan)</h5>
            <table class="striped responsive-table">
                <thead>
                    <tr><th>No. Anggota</th><th>Nama</th><th>No. Kredit</th><th>Jumlah</th><th>Tanggal</th></tr>
                </thead>
                <tbody>
                @forelse($realisasi as $r)
                    <tr>
                        <td>{{ $r['MEMBER_NO'] ?? '-' }}</td>
                        <td>{{ $r['MEMBER_NAME'] ?? '-' }}</td>
                        <td>{{ $r['LOAN_ACCOUNT_NO'] ?? '-' }}</td>
                        <td>{{ number_format($r['AMOUNT'] ?? 0, 2) }}</td>
                        <td>{{ $r['TRANSACTION_DATE'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="center">Belum ada realisasi hari ini.</td></tr>
                @endforelse
                </tbody>
            </table>
            <h5 style="font-size:1rem; color:#555">Angsuran</h5>
            <table class="striped responsive-table">
                <thead>
                    <tr><th>No. Anggota</th><th>Nama</th><th>No. Kredit</th><th>Angsuran</th><th>Jumlah</th><th>Dibayar</th></tr>
                </thead>
                <tbody>
                @forelse($angsuran as $r)
                    <tr>
                        <td>{{ $r['MEMBER_NO'] ?? '-' }}</td>
                        <td>{{ $r['MEMBER_NAME'] ?? '-' }}</td>
                        <td>{{ $r['LOAN_ACCOUNT_NO'] ?? '-' }}</td>
                        <td>{{ $r['INSTALLMENT_NO'] ?? 0 }}</td>
                        <td>{{ number_format($r['AMOUNT'] ?? 0, 2) }}</td>
                        <td>{{ $r['PAID_DATE'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="center">Belum ada angsuran dibayar hari ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
