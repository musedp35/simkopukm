@extends('admin.layouts')
@section('title', 'Form Realisasi')

@section('admin-content')
<div class="container">
    <h4 class="page-title">Form Realisasi Pinjaman</h4>
    <div class="card">
        <div class="card-content">
            <form method="POST" action="{{ route('admin.loan.proses_realisasi') }}">
                @csrf
                <div class="row">
                    <div class="input-field col s6">
                        <input type="text" name="loanAccountNo" id="loanAccountNo" required>
                        <label for="loanAccountNo">No. Kredit / Loan Account No</label>
                    </div>
                    <div class="input-field col s6">
                        <input type="number" name="amount" id="amount" step="0.01" required>
                        <label for="amount">Jumlah Realisasi</label>
                    </div>
                </div>
                <button type="submit" class="btn amber waves-effect">Catat Realisasi</button>
                <a href="{{ route('admin.loan.laporan_realisasi') }}" class="btn blue darken-1 waves-effect">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
