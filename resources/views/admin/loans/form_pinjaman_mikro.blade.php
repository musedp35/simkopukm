@extends('admin.layouts')
@section('title', 'Pinjaman Mikro')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Form Pinjaman Mikro</h4>
    <div class="card">
        <div class="card-content">
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>No. Anggota</th><th>Nama</th>
                        <th>Pokok</th><th>Persentase</th><th>Lama Pinjaman</th>
                        <th>Angsuran</th><th>Kondisi</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pinjaman as $p)
                    <tr>
                        <td>{{ $p->MEMBER_NO ?? '-' }}</td>
                        <td>{{ $p->MEMBER_NAME ?? '-' }}</td>
                        <td>{{ number_format($p->LOAN_AMOUNT ?? 0, 2) }}</td>
                        <td>{{ $p->LOAN_PERCENT ?? 0 }}%</td>
                        <td>{{ $p->INSTALLMENT_TOTAL ?? 0 }}</td>
                        <td>{{ number_format($p->INSTALLMENT_AMOUNT ?? 0, 2) }}</td>
                        <td>{{ ($p->REMAINING_AMOUNT ?? 0) < 5000000 ? 'Kritis' : 'Baik' }}</td>
                        <td>{{ $p->LOAN_STATUS_ID }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="center">Belum ada data peminjam Mikro.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
