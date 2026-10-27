@extends('admin.layouts')
@section('title', 'Agunan — '.e($no))

@section('admin-content')
<div class="container">
    <div class="row" style="margin-bottom:1rem">
        <h4 class="page-title">Data Agunan <span class="text-muted">({{ $no }})</span></h4>
        <a href="{{ route('admin.loan.pj_pinjaman') }}" class="btn btn-sm btn-secondary">&larr; Kembali</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('info'))<div class="alert alert-info">{{ session('info') }}</div>@endif

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
                </div>
            @endif
        </div>
    </div>

    {{-- Status Foto Agunan --}}
    <div class="card">
        <div class="card-header"><span><i class="fas fa-image"></i> Foto Agunan</span></div>
        <div class="card-content">
            @if($imgAgunan && $imgAgunan->IMAGE_FILE)
                <div class="mb-3">
                    <p class="text-success"><i class="fas fa-check-circle"></i> Foto agunan tersimpan.</p>
                    <a href="{{ asset('storage/'.$imgAgunan->IMAGE_FILE) }}" target="_blank" class="btn btn-sm btn-outline">
                        <i class="fas fa-eye"></i> Lihat Foto Agunan
                    </a>
                    <p class="text-muted text-small mt-1">Terakhir diupdate: {{ $imgAgunan->LOGTIME ?? '-' }}</p>
                </div>
            @else
                <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> Belum ada foto agunan.</p>
            @endif
        </div>
    </div>

    {{-- Form Upload --}}
    <div class="card">
        <div class="card-header"><span><i class="fas fa-upload"></i> Upload Foto Agunan</span></div>
        <div class="card-content">
            <form action="{{ route('admin.loan.proses_agunan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="loan_account_no" value="{{ $no }}">

                <div class="row">
                    <div class="col s12 m8">
                        <label class="form-label">Pilih File Foto Agunan (PDF/JPG/PNG, max 5 MB)</label>
                        <input type="file" name="foto_agunan" class="file-input"
                               accept=".pdf,.jpg,.jpeg,.png"
                               @required(!($imgAgunan && $imgAgunan->IMAGE_FILE)) />
                    </div>
                    <div class="col s12 m4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Aksi --}}
    <div class="card">
        <div class="card-header"><span>Aksi</span></div>
        <div class="card-content">
            <div class="row">
                <div class="col s12 m3 l3"><a href="{{ route('admin.loan.form_survey',['no'=>$no]) }}" class="btn btn-outline"><i class="fas fa-clipboard-list"></i> Survey</a></div>
                <div class="col s12 m3 l3"><a href="{{ route('admin.loan.pelunasan',['no'=>$no]) }}" class="btn btn-outline"><i class="fas fa-check-double"></i> Pelunasan</a></div>
                <div class="col s12 m3 l3"><a href="{{ route('admin.loan.log_ao',['no'=>$no]) }}" class="btn btn-outline"><i class="fas fa-list"></i> Log AO/PJ</a></div>
            </div>
        </div>
    </div>
</div>
@endsection
