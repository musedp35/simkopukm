@extends('admin.layouts')
@section('title', 'Pinjaman UKM')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Form Pinjaman UKM</h4>
    <div class="card">
        <div class="card-content">
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>No. Anggota</th><th>Nama</th><th>Produksi</th>
                        <th>Pokok</th><th>Persentase</th><th>Lama Pinjaman</th>
                        <th>Angsuran</th><th>Kondisi</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pinjaman as $p)
                    <tr>
                        <td>{{ $p->MEMBER_NO ?? '-' }}</td>
                        <td>{{ $p->MEMBER_NAME ?? '-' }}</td>
                        <td>{{ $p->LOAN_PRODUCT_ID ?? '-' }}</td>
                        <td>{{ number_format($p->LOAN_AMOUNT ?? 0, 2) }}</td>
                        <td>{{ $p->LOAN_PERCENT ?? 0 }}%</td>
                        <td>{{ $p->INSTALLMENT_TOTAL ?? 0 }}</td>
                        <td>{{ number_format($p->INSTALLMENT_AMOUNT ?? 0, 2) }}</td>
                        <td>
                            @if(($p->REMAINING_AMOUNT ?? 0) < 10000000)
                                <span class="red-text">Berisiko</span>
                            @elseif(($p->REMAINING_AMOUNT ?? 0) < 5000000)
                                <span class="red-text bold">Kritis</span>
                            @else
                                <span class="green-text">Baik</span>
                            @endif
                        </td>
                        <td>{{ $p->LOAN_STATUS_ID }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="center">Belum ada data peminjam UKM.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
