@extends('admin.layouts')
@section('title', 'Transaksi SSB - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Transaksi SSB (Deposito)</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>

    <form method="GET" action="{{ route('admin.savings.trans_ssb') }}">
        <div class="row">
            <div class="col s12 m6"><div class="input-field">
                <input type="text" name="idNoAccSSB" value="{{ request('idNoAccSSB') }}" required>
                <label>No. Akun SSB</label>
            </div></div>
            <div class="col s12 m6"><button type="submit" class="btn blue"><i class="material-icons left">search</i> Cari</button></div>
        </div>
    </form>

    @if($detailSsb)
    @php $d = $detailSsb; @endphp
    <hr>
    <h6 class="blue-text">Detail Akun SSB</h6>
    <div class="table-responsive">
        <table class="highlight">
            <tbody>
                <tr><th>No. Akun</th><td><strong>{{ $d['SAVINGS_ACCOUNT_NO'] ?? '-' }}</strong></td></tr>
                <tr><th>Saldo</th><td>Rp {{ number_format((float)($d['SAVINGS_BALANCE'] ?? 0), 2) }}</td></tr>
                <tr><th>Status</th><td>{{ $d['ACTIVE_ACCOUNT_ID'] == 1 ? 'Aktif' : 'Nonaktif' }}</td></tr>
            </tbody>
        </table>
    </div>

    <h6 class="blue-text" style="margin-top:1rem;">Form Transaksi SSB</h6>
    <form method="POST" action="{{ route('admin.savings.proses_trans_ssb') }}">
        @csrf
        <input type="hidden" name="edtNoacc" value="{{ $d['SAVINGS_ACCOUNT_NO'] ?? '' }}">
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
                <input type="number" step="0.01" name="edtSimpokok" value="0">
                <label>Nominal (Rp)</label>
            </div></div>
            <div class="col s6 m3"><div class="input-field">
                <input type="text" name="edtRef">
                <label>No. Referensi</label>
            </div></div>
        </div>
        <button type="submit" class="btn blue accent-4" onclick="return confirm('Proses transaksi SSB?')">
            <i class="material-icons left">check</i> Proses Transaksi
        </button>
    </form>
    @endif
</div></div>
@stop
