@extends('admin.layouts')
@section('title', 'Laporan SPH')

@section('admin-content')
<div class="container">
    <div class="row" style="margin-bottom:1rem">
        <h4 class="page-title">Laporan SPH <small class="text-muted">(Pinjaman Status &gt; 6)</small></h4>
        <a href="{{ route('admin.loan.pj_pinjaman') }}" class="btn btn-sm btn-secondary">&larr; Kembali</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="card">
        <div class="card-header">
            <span><i class="fas fa-file-alt"></i> Daftar Peminjam — Status Pelunas / Rescheduling</span>
        </div>
        <div class="card-content">
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>No. Anggota</th>
                        <th>Nama</th>
                        <th>No. Kredit</th>
                        <th>Jumlah Pinjaman</th>
                        <th>Agunan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rows as $r)
                    <tr>
                        <td>{{ $r['MEMBER_NO'] ?? '-' }}</td>
                        <td>{{ $r['MEMBER_NAME'] ?? '-' }}</td>
                        <td><strong>{{ $r['LOAN_ACCOUNT_NO'] ?? '-' }}</strong></td>
                        <td>Rp {{ number_format($r['LOAN_AMOUNT'] ?? 0, 2) }}</td>
                        <td>{{ $r['LOAN_COLLATERAL_NAME'] ?? '-' }}</td>
                        <td>
                            <span class="badge badge-info">{{ $r['LOAN_TRACING_STATUS_NAME'] ?? 'Pelunas/Resched' }}</span>
                        </td>
                        <td style="white-space:nowrap">
                            <a href="{{ route('admin.loan.cetak_sph_kecil', ['no' => $r['LOAN_ACCOUNT_NO'] ?? '']) }}"
                               class="btn btn-xs btn-outline" title="Cetak SPH Kecil">
                                <i class="fas fa-print"></i> Kecil
                            </a>
                            <a href="{{ route('admin.loan.cetak_sph_besar', ['no' => $r['LOAN_ACCOUNT_NO'] ?? '']) }}"
                               class="btn btn-xs btn-outline" title="Cetak SPH Besar">
                                <i class="fas fa-file-pdf"></i> Besar
                            </a>
                            <a href="{{ route('admin.loan.cetak_sph_pdf', ['no' => $r['LOAN_ACCOUNT_NO'] ?? '', 'agunanId' => 0, 'size' => 'kecil']) }}"
                               target="_blank" class="btn btn-xs btn-primary" title="Unduh PDF">
                                <i class="fas fa-download"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="center text-muted">Belum ada pinjaman dengan status pelunas/rescheduling.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
