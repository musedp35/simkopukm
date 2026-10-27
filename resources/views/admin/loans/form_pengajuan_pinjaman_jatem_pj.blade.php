@extends('admin.layouts')
@section('title', 'Pengajuan Jatuh Tempo')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Form Pengajuan Jatuh Tempo</h4>
    <div class="card">
        <div class="card-content">
            <form id="form-pengajuan-jatem" method="POST" action="{{ url('admin/loan/pengajuan-jatem/proses') }}">
                @csrf
                <div class="row">
                    <div class="input-field col s6">
                        <input type="text" name="noKredit" id="noKredit" required>
                        <label for="noKredit">No. Kredit</label>
                    </div>
                    <div class="input-field col s6">
                        <input type="text" name="namaAnggota" id="namaAnggota" readonly>
                        <label for="namaAnggota">Nama Anggota</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s4">
                        <input type="number" name="loanAmount" id="loanAmountJatem" required>
                        <label for="loanAmountJatem">Jumlah Pinjaman</label>
                    </div>
                    <div class="input-field col s4">
                        <input type="number" name="installmentTotal" id="installmentTotalJatem" required>
                        <label for="installmentTotalJatem">Jumlah Angsuran</label>
                    </div>
                    <div class="input-field col s4">
                        <input type="number" name="loanPercent" id="loanPercentJatem" step="0.01" required>
                        <label for="loanPercentJatem">Bunga (%/tahun)</label>
                    </div>
                </div>
                <button type="submit" class="btn waves-effect waves-light">Simpan Pengajuan</button>
                <a href="{{ url('admin/loan/pj-pinjaman') }}" class="btn waves-effect waves-light blue darken-1">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
