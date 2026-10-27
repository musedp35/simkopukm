@extends('admin.layouts')
@section('title', 'Pelunasan — '.e($no))

@section('admin-content')
<div class="container">
    <div class="row" style="margin-bottom:1rem">
        <h4 class="page-title">Pelunasan <span class="text-muted">({{ $no }})</span></h4>
        <a href="{{ route('admin.loan.pj_pinjaman') }}" class="btn btn-sm btn-secondary">&larr; Kembali</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    @if(session('info'))<div class="alert alert-info">{{ session('info') }}</div>@endif

    <div class="card">
        <div class="card-header"><span><i class="fas fa-user"></i> Data Peminjam</span></div>
        <div class="card-content">
            <div class="row">
                <div class="col s12 m4 l3"><label class="form-label">No. Anggota</label><p class="text-value">{{ $account->MEMBER_NO ?? '-' }}</p></div>
                <div class="col s12 m4 l3"><label class="form-label">Nama</label><p class="text-value">{{ $account->MEMBER_NAME ?? '-' }}</p></div>
                <div class="col s12 m4 l3"><label class="form-label">Jumlah Pinjaman</label><p class="text-value">Rp {{ number_format($account->LOAN_AMOUNT ?? 0, 2) }}</p></div>
                <div class="col s12 m4 l3"><label class="form-label">Total Angsuran</label><p class="text-value">Rp {{ number_format($totalAngsuran ?? 0, 2) }}</p></div>
                <div class="col s12 m4 l3"><label class="form-label">Sisa</label><p class="text-value text-danger">Rp {{ number_format($sisa ?? 0, 2) }}</p></div>
                <div class="col s12 m4 l3"><label class="form-label">Status</label><p class="text-value">{{ $account->LOAN_STATUS_ID ?? '-' }}</p></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span><i class="fas fa-check-double"></i> Proses Pelunasan</span></div>
        <div class="card-content">
            <form action="{{ route('admin.loan.proses_pelunasan_detail') }}" method="POST">
                @csrf
                <input type="hidden" name="loan_account_no" value="{{ $no }}">

                <div class="row">
                    <div class="col s12 m6">
                        <label class="form-label">Catatan</label>
                        <textarea name="note" rows="3" class="form-control"
                                  placeholder="Catatan pelunasan (opsional)">{{ old('note') }}</textarea>
                    </div>
                </div>

                <div class="mt-3 row">
                    <div class="col s12">
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Yakin ingin memproses pelunasan untuk akun {{ $no }}?')">
                            <i class="fas fa-check-double"></i> Proses Pelunasan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span>Aksi</span></div>
        <div class="card-content">
            <div class="row">
                <div class="col s12 m3 l3"><a href="{{ route('admin.loan.form_survey',['no'=>$no]) }}" class="btn btn-outline"><i class="fas fa-clipboard-list"></i> Survey</a></div>
                <div class="col s12 m3 l3"><a href="{{ route('admin.loan.agunan',['no'=>$no]) }}" class="btn btn-outline"><i class="fas fa-handshake"></i> Agunan</a></div>
                <div class="col s12 m3 l3"><a href="{{ route('admin.loan.log_ao',['no'=>$no]) }}" class="btn btn-outline"><i class="fas fa-list"></i> Log AO/PJ</a></div>
            </div>
        </div>
    </div>
</div>
@endsection
