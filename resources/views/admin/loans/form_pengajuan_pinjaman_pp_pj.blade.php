@extends('admin.layouts')
@section('title', 'Pengajuan Pinjaman PP')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Form Pengajuan Pinjaman Payroll (PP)</h4>
    <div class="card">
        <div class="card-content">
            <div class="row">
                <div class="col s12">
                    <p>No. Kredit: <strong>{{ $noCredit ?? '-' }}</strong></p>
                    @if($dataPeminjam)
                        <p>Nama: <strong>{{ $dataPeminjam['MEMBER_NAME'] ?? '-' }}</strong></p>
                        <p>No. Anggota: {{ $dataPeminjam['MEMBER_NO'] ?? '-' }}</p>
                    @endif
                </div>
            </div>
            <form id="form-pengajuan-pp" method="POST" action="{{ url('admin/loan/pengajuan-pp/proses') }}">
                @csrf
                <div class="row">
                    <div class="input-field col s6">
                        <input type="number" name="loanAmount" id="loanAmount" required>
                        <label for="loanAmount">Jumlah Pinjaman</label>
                    </div>
                    <div class="input-field col s3">
                        <input type="number" name="loanPercent" id="loanPercent" step="0.01" required>
                        <label for="loanPercent">Bunga (%/tahun)</label>
                    </div>
                    <div class="input-field col s3">
                        <input type="number" name="installmentTotal" id="installmentTotal" required>
                        <label for="installmentTotal">Jumlah Angsuran</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s6">
                        <input type="text" name="loanProduct" id="loanProduct" readonly value="{{ $dataPeminjam['LOAN_PRODUCT_ID'] ?? 'LOAN004' }}">
                        <label for="loanProduct">Produk</label>
                    </div>
                </div>
                <button type="submit" class="btn waves-effect waves-light">Simpan Pengajuan</button>
                <a href="{{ url('admin/loan/ukm') }}" class="btn waves-effect waves-light blue darken-1">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
