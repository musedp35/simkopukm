@extends('admin.layouts')
@section('title', 'Rescheduling by Request')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Form Rescheduling by Request</h4>
    <div class="card">
        <div class="card-content">
            <form id="form-rescheduling-req" method="POST" action="{{ url('admin/loan/pengajuan-rsbyreq/proses') }}">
                @csrf
                <div class="row">
                    <div class="input-field col s4">
                        <input type="text" name="noKredit" id="noKreditRSReq" required>
                        <label for="noKreditRSReq">No. Kredit</label>
                    </div>
                    <div class="input-field col s4">
                        <input type="text" name="requestDate" id="requestDate" required>
                        <label for="requestDate">Tanggal Request</label>
                    </div>
                    <div class="input-field col s4">
                        <input type="number" name="newInstallmentTotal" id="newInstallmentReq" required>
                        <label for="newInstallmentReq">Jumlah Angsuran Baru</label>
                    </div>
                </div>
                <button type="submit" class="btn waves-effect waves-light">Simpan Rescheduling</button>
                <a href="{{ url('admin/loan/pj-pinjaman') }}" class="btn waves-effect waves-light blue darken-1">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
