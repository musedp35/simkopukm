@extends('admin.layouts')
@section('title', 'Log AO/PJ — '.e($no))

@section('admin-content')
<div class="container">
    <div class="row" style="margin-bottom:1rem">
        <h4 class="page-title">Log Aksi AO &amp; PJ <span class="text-muted">({{ $no }})</span></h4>
        <a href="{{ route('admin.loan.pj_pinjaman') }}" class="btn btn-sm btn-secondary">&larr; Kembali</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    {{-- Info Peminjam --}}
    <div class="card">
        <div class="card-header"><span><i class="fas fa-user"></i> Data Peminjam</span></div>
        <div class="card-content">
            @if($account)
                <div class="row">
                    <div class="col s12 m4 l3"><label class="form-label">No. Anggota</label><p class="text-value">{{ $account->MEMBER_NO ?? '-' }}</p></div>
                    <div class="col s12 m4 l3"><label class="form-label">Nama</label><p class="text-value">{{ $account->MEMBER_NAME ?? '-' }}</p></div>
                    <div class="col s12 m4 l3"><label class="form-label">No. Kredit</label><p class="text-value">{{ $no }}</p></div>
                    <div class="col s12 m4 l3"><label class="form-label">Jumlah</label><p class="text-value">Rp {{ number_format($account->LOAN_AMOUNT ?? 0, 2) }}</p></div>
                    <div class="col s12 m4 l3"><label class="form-label">Sisa</label><p class="text-value">Rp {{ number_format($account->REMAINING_AMOUNT ?? 0, 2) }}</p></div>
                    <div class="col s12 m4 l3"><label class="form-label">Status</label><p class="text-value">{{ $account->LOAN_STATUS_ID ?? '-' }}</p></div>
                </div>
            @endif
        </div>
    </div>

    {{-- Tabel Log --}}
    <div class="card">
        <div class="card-header"><span><i class="fas fa-list"></i> Riwayat Perubahan Status</span></div>
        <div class="card-content">
            <table class="striped responsive-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tgl</th>
                        <th>Status Awal</th>
                        <th>Status Baru</th>
                        <th>Ditindakkan Oleh</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($logs as $idx => $log)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $log->UPDATED_AT ? date('d/m/Y H:i', strtotime($log->UPDATED_AT)) : '-' }}</td>
                        <td>{{ $log->STATUS_BEFORE ?? '-' }}</td>
                        <td>{{ $log->LOAN_TRACING_STATUS_ID_AFTER ?? '-' }}</td>
                        <td>{{ $log->UPDATED_BY ?? '-' }}</td>
                        <td>{{ $log->NOTE ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="center text-muted">Belum ada log perubahan status.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Aksi --}}
    <div class="card">
        <div class="card-header"><span>Aksi</span></div>
        <div class="card-content">
            <div class="row">
                <div class="col s12 m3 l3"><a href="{{ route('admin.loan.form_survey',['no'=>$no]) }}" class="btn btn-outline"><i class="fas fa-clipboard-list"></i> Survey</a></div>
                <div class="col s12 m3 l3"><a href="{{ route('admin.loan.pelunasan',['no'=>$no]) }}" class="btn btn-outline"><i class="fas fa-check-double"></i> Pelunasan</a></div>
                <div class="col s12 m3 l3"><a href="{{ route('admin.loan.agunan',['no'=>$no]) }}" class="btn btn-outline"><i class="fas fa-handshake"></i> Agunan</a></div>
            </div>
        </div>
    </div>
</div>
@endsection
