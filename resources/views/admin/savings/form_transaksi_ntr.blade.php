@extends('admin.layouts')
@section('title', 'Transaksi NTR - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Transaksi NTR (Setor / Tarik)</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>

    <form method="GET" action="{{ route('admin.savings.trans_ntr_form') }}">
        <div class="row">
            <div class="col s12 m6"><div class="input-field">
                <input type="text" name="idAgt" value="{{ request('idAgt') }}" required>
                <label>No. Anggota / No. Registry</label>
            </div></div>
            <div class="col s12 m6"><button type="submit" class="btn blue"><i class="material-icons left">search</i> Cari</button></div>
        </div>
    </form>

    @if(request('idAgt'))
    @if($checkTransNtr && ($checkTransNtr['statusNTR'] ?? 0) > 0)
        <div class="card orange lighten-5"><div class="card-content orange-text">
            Anggota ini sudah memiliki transaksi NTR dengan status approval = 0 (menunggu persetujuan).
        </div></div>
    @endif

    <hr>
    <h6 class="blue-text">Form Transaksi NTR</h6>
    <form method="POST" action="{{ route('admin.savings.proses_trans_ntr') }}">
        @csrf
        <input type="hidden" name="edtNoacc" value="{{ request('idAgt') }}">
        <input type="hidden" name="edtNotrans" value="{{ $noTrans }}">
        <div class="row">
            <div class="col s6 m3"><div class="input-field">
                <select name="cmbJnsTransaksi" class="browser-default">
                    <option value="1">Setor</option>
                    <option value="2">Tarik</option>
                </select>
                <label>Jenis Transaksi</label>
            </div></div>
            <div class="col s6 m3"><div class="input-field">
                <select name="cmbJnsPembayaran" class="browser-default">
                    <option value="1">Tunai</option>
                    <option value="2">Transfer</option>
                </select>
                <label>Jenis Pembayaran</label>
            </div></div>
            <div class="col s6 m3"><div class="input-field">
                <input type="number" step="0.01" name="edtSimwajib" value="0">
                <label>Simpanan Wajib</label>
            </div></div>
            <div class="col s6 m3"><div class="input-field">
                <input type="number" step="0.01" name="edtSimpokok" value="0">
                <label>Simpanan Pokok</label>
            </div></div>
        </div>
        <div class="row">
            <div class="col s12 m4"><div class="input-field">
                <input type="text" name="edtRef">
                <label>No. Referensi / Keterangan</label>
            </div></div>
        </div>
        <button type="submit" class="btn blue accent-4" onclick="return confirm('Proses transaksi NTR?')">
            <i class="material-icons left">check</i> Proses Transaksi
        </button>
    </form>
    @endif
</div></div>
@stop
