@extends('admin.layouts')
@section('title', 'Survey — '.e($no))

@section('admin-content')
<div class="container">
    <div class="row" style="margin-bottom:1rem">
        <h4 class="page-title">Form Survey <span class="text-muted">({{ $no }})</span></h4>
        <a href="{{ route('admin.loan.pj_pinjaman') }}" class="btn btn-sm btn-secondary">&larr; Kembali</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    {{-- Info --}}
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

    {{-- Form Upload --}}
    <div class="card">
        <div class="card-header"><span><i class="fas fa-upload"></i> Upload Berkas Survey</span></div>
        <div class="card-content">
            <form action="{{ route('admin.loan.proses_survey') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="loan_account_no" value="{{ $no }}">

                <div class="row">
                    <div class="col s12 m6">
                        <label class="form-label">File Survey (PDF/IMG)</label>
                        <input type="file" name="file_survey_01" class="file-input"
                               accept=".pdf,.jpg,.jpeg,.png" />
                        @if($imgSurvey?->IMAGE_FILE)
                            <p class="text-small text-success mt-1">
                                <i class="fas fa-check"></i> File tersimpan:
                                <a href="{{ asset('storage/'.$imgSurvey->IMAGE_FILE) }}" target="_blank">Lihat</a>
                            </p>
                        @endif
                    </div>
                    <div class="col s12 m6">
                        <label class="form-label">Foto Agunan (PDF/IMG)</label>
                        <input type="file" name="file_survey_02" class="file-input"
                               accept=".pdf,.jpg,.jpeg,.png" />
                        @if($imgAgunan?->IMAGE_FILE)
                            <p class="text-small text-success mt-1">
                                <i class="fas fa-check"></i> Foto tersimpan:
                                <a href="{{ asset('storage/'.$imgAgunan->IMAGE_FILE) }}" target="_blank">Lihat</a>
                            </p>
                        @endif
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Survey</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Aksi --}}
    <div class="card">
        <div class="card-header"><span>Aksi</span></div>
        <div class="card-content">
            <div class="row">
                <div class="col s12 m3 l3"><a href="{{ route('admin.loan.pelunasan',['no'=>$no]) }}" class="btn btn-outline"><i class="fas fa-check-double"></i> Pelunasan</a></div>
                <div class="col s12 m3 l3"><a href="{{ route('admin.loan.agunan',['no'=>$no]) }}" class="btn btn-outline"><i class="fas fa-handshake"></i> Agunan</a></div>
                <div class="col s12 m3 l3"><a href="{{ route('admin.loan.log_ao',['no'=>$no]) }}" class="btn btn-outline"><i class="fas fa-list"></i> Log AO/PJ</a></div>
            </div>
        </div>
    </div>
</div>
@endsection
