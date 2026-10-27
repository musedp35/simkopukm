@extends('admin.layouts')
@section('title', 'Pengajuan Pinjaman MG — Emas')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Form Pengajuan Pinjaman Multiguna (MG) — Agunan Emas</h4>
    <div class="row">
        {{-- Info Peminjam --}}
        <div class="col s12 m6">
            <div class="card">
                <div class="card-header"><span><i class="fas fa-user"></i> Data Peminjam</span></div>
                <div class="card-content">
                    @if($dataAgt)
                        <div class="row">
                            <div class="col s6"><label class="form-label">No. Anggota</label><p class="text-value">{{ $dataAgt['MEMBER_NO'] ?? '-' }}</p></div>
                            <div class="col s6"><label class="form-label">Nama</label><p class="text-value">{{ $dataAgt['MEMBER_NAME'] ?? '-' }}</p></div>
                            <div class="col s12"><label class="form-label">Alamat</label><p class="text-value">{{ $dataAgt['MEMBER_ADDRESS'] ?? '-' }}</p></div>
                            <div class="col s12"><label class="form-label">Foto</label>
                                @if(!empty($dataAgt['IMAGE_FILE']))
                                    <img src="{{ asset('storage/' . $dataAgt['IMAGE_FILE']) }}" alt="Foto Anggota" style="max-height:120px;">
                                @else
                                    <span class="text-muted">Belum ada foto</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-muted">
                            @if($noAgt)
                                Peminjam <strong>{{ $noAgt }}</strong> tidak ditemukan atau tidak memenuhi syarat.
                            @else
                                Silakan masukkan No. Anggota / Registry pada bilah pencarian di atas.
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Jasa & Provisi --}}
        <div class="col s12 m6">
            <div class="card">
                <div class="card-header"><span><i class="fas fa-percentage"></i> Jasa (Bunga)</span></div>
                <div class="card-content">
                    <table class="striped responsive-table">
                        <thead><tr><th>Tenure</th><th>Rate %</th></tr></thead>
                        <tbody>
                        @forelse($jasa as $j)
                            <tr><td>{{ $j['LOAN_PERIOD'] ?? '-' }} bulan</td><td>{{ $j['LOAN_PERCENT'] ?? 0 }}%</td></tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted">Belum ada data jasa.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><span><i class="fas fa-hand-holding-usd"></i> Provisi</span></div>
                <div class="card-content">
                    <table class="striped responsive-table">
                        <thead><tr><th>Tenure</th><th>Provisi</th></tr></thead>
                        <tbody>
                        @forelse($provisi as $p)
                            <tr><td>{{ $p['LOAN_PERIOD'] ?? '-' }} bulan</td><td>{{ $p['PROVISION_RATE'] ?? '-' }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted">Belum ada data provisi.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <div class="card">
        <div class="card-header"><span><i class="fas fa-file-alt"></i> Form Pengajuan</span></div>
        <div class="card-content">
            @if($dataAgt)
                <form id="form-pengajuan-mg-emas" method="POST" action="{{ route('admin.loan.proses_pengajuan_mg') }}">
                    @csrf
                    <input type="hidden" name="memberRegistry" value="{{ $dataAgt['MEMBER_REGISTRY'] }}">
                    <input type="hidden" name="NO_CREDIT"    value="{{ $noCredit['noCredit'] ?? '' }}">

                    <div class="row">
                        <div class="col s12 m4 l4">
                            <input type="number" name="LOAN_AMOUNT" step="0.01" min="0" required
                                   placeholder="Jumlah pinjaman (Rp)" class="form-input">
                            <label class="form-label">Jumlah Pinjaman (Rp)</label>
                        </div>
                        <div class="col s12 m4 l4">
                            <input type="number" name="LOAN_PERIOD" min="1" max="120" required
                                   placeholder="Jumlah angsuran (bulan)" class="form-input">
                            <label class="form-label">Jumlah Angsuran (bulan)</label>
                        </div>
                        <div class="col s12 m4 l4">
                            <input type="text" readonly value="{{ $noCredit['noCredit'] ?? '-' }}" class="form-input">
                            <label class="form-label">No. Kredit</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col s12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Pengajuan MG Emas
                            </button>
                            <a href="{{ route('admin.loan.pj_pinjaman') }}" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </form>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Silakan cari peminjam terlebih dahulu (query parameter <code>noAgt</code>).
                    Contoh: <code>?noAgt=ANGGOTA001</code>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
