@extends('admin.layouts')
@section('title', 'Transaksi Simpanan NTR - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Transaksi Simpanan NTR</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>

    <form method="GET" action="{{ route('admin.savings.trans_ntr') }}">
        <div class="row">
            <div class="col s12 m4"><div class="input-field">
                <input type="text" name="idHiddenSimNTR" value="{{ request('idHiddenSimNTR') }}" required>
                <label>No. Anggota / No. KTP</label>
            </div></div>
            <div class="col s12 m4"><button type="submit" class="btn blue"><i class="material-icons left">search</i> Cari</button></div>
        </div>
    </form>

    @if(request('idHiddenSimNTR'))
    <table class="highlight">
        <tbody>
            <tr><th>Status Anggota NTR</th><td>{{ $statAgt == '1' ? 'Sudah NTR (Approval)' : 'Belum NTR' }}</td></tr>
            @if($besaranSimpPokokWjb)
            <tr><th>Simpanan Pokok Wajib</th><td>{{ $besaranSimpPokokWjb['MIN_BALANCE'] ?? '0' }}</td></tr>
            @endif
            <tr><th>Akumulasi Simp. Wajib</th><td>{{ $edtSimWajibAkum['SAVINGS_BALANCE'] ?? 0 }}</td></tr>
        </tbody>
    </table>

    @if(!empty($detailTransNTR))
    <h6 class="blue-text" style="margin-top:1rem;">Transaksi NTR (approved)</h6>
    <p class="grey-text">{{ count($detailTransNTR) }} transaksi NTR tercantum.</p>
    @endif

    <h6 class="blue-text" style="margin-top:1rem;">Produk Simpanan</h6>
    <select class="browser-default" name="SAVINGS_PRODUCT_ID" disabled>
        @foreach($transaksiAcc as $acc)
            <option value="{{ $acc['SIM001'] ?? $acc['SIM002'] ?? '' }}">{{ $acc['SIM001'] ?? $acc['SIM002'] ?? '' }} - {{ $acc['PRODUCT_NAME'] ?? '' }}</option>
        @endforeach
    </select>
    @endif
</div></div>
@stop
