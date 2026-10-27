@extends('admin.layouts')
@section('title', 'Pinjaman Jaminan')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Form Pinjaman Jaminan</h4>
    <div class="card">
        <div class="card-content">
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>No. Anggota</th><th>Nama</th><th>Produksi</th>
                        <th>Pokok</th><th>Angsuran</th><th>Kondisi</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pinjaman as $p)
                    <tr>
                        <td>{{ $p->MEMBER_NO ?? '-' }}</td>
                        <td>{{ $p->MEMBER_NAME ?? '-' }}</td>
                        <td>{{ $p->LOAN_PRODUCT_ID ?? '-' }}</td>
                        <td>{{ number_format($p->LOAN_AMOUNT ?? 0, 2) }}</td>
                        <td>{{ number_format($p->INSTALLMENT_AMOUNT ?? 0, 2) }}</td>
                        <td>{{ ($p->REMAINING_AMOUNT ?? 0) < 5000000 ? 'Kritis' : 'Baik' }}</td>
                        <td>{{ $p->LOAN_STATUS_ID }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="center">Belum ada data peminjam Jaminan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
