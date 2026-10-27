@extends('admin.layouts')
@section('title', 'Rescheduling Jatuh Tempo')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Form Rescheduling Jatuh Tempo</h4>
    <div class="card">
        <div class="card-content">
            <form id="form-rescheduling-jt" method="POST" action="{{ url('admin/loan/pengajuan-rsjatem/proses') }}">
                @csrf
                <div class="row">
                    <div class="input-field col s6">
                        <input type="text" name="noKredit" id="noKreditRS" required>
                        <label for="noKreditRS">No. Kredit</label>
                    </div>
                    <div class="input-field col s6">
                        <input type="number" name="newInstallmentTotal" id="newInstallmentTotal" required>
                        <label for="newInstallmentTotal">Jumlah Angsuran Baru</label>
                    </div>
                </div>
                <button type="submit" class="btn waves-effect waves-light">Simpan Rescheduling</button>
                <a href="{{ url('admin/loan/pj-pinjaman') }}" class="btn waves-effect waves-light blue darken-1">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
