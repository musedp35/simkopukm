@extends('admin.layouts')
@section('title', 'Penutupan Simpanan SSB - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Penutupan Simpanan SSB</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>

    <form method="GET" action="{{ route('admin.savings.tutup_rek_ssb') }}">
        <div class="row">
            <div class="col s12 m6"><div class="input-field">
                <input type="text" name="idHiddenSSB" value="{{ request('idHiddenSSB') }}" required>
                <label>No. Anggota / No. KTP / No. Akun</label>
            </div></div>
            <div class="col s12 m6"><button type="submit" class="btn blue"><i class="material-icons left">search</i> Cari</button></div>
        </div>
    </form>

    @if($detailSimSSB)
    @php $d = $detailSimSSB; @endphp
    <hr>
    <h6 class="blue-text">Data SSB</h6>
    <div class="table-responsive">
        <table class="highlight">
            <tbody>
                <tr><th>No. Akun</th><td>{{ $d['SAVINGS_ACCOUNT_NO'] ?? '-' }}</td></tr>
                <tr><th>Nama</th><td>{{ $d['MEMBER_NAME'] ?? '-' }}</td></tr>
                <tr><th>No. Anggota</th><td>{{ $d['MEMBER_NO'] ?? '-' }}</td></tr>
                <tr><th>Saldo</th><td>Rp {{ number_format((float)($d['SAVINGS_BALANCE'] ?? 0), 2) }}</td></tr>
                @if(isset($pembulatan) && $pembulatan !== '')
                <tr><th>Pembulatan</th><td>Rp {{ number_format((float)$pembulatan, 2) }}</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    <h6 class="blue-text" style="margin-top:1rem;">Proses Penutupan SSB</h6>
    <form method="POST" action="{{ route('admin.savings.proses_penutupan_ssb') }}">
        @csrf
        <input type="hidden" name="edtNoAcc" value="{{ $d['SAVINGS_ACCOUNT_NO'] ?? request('idHiddenSSB') }}">
        <div class="row">
            <div class="col s12 m6"><div class="input-field">
                <input type="text" name="keterangan" value="Penutupan Rekening SSB">
                <label>Keterangan</label>
            </div></div>
        </div>
        <button type="submit" class="btn red darken-1" onclick="return confirm('Yakin tutup rekening SSB ini?')">
            <i class="material-icons left">delete</i> Tutup Rekening
        </button>
    </form>
    @endif
</div></div>
@stop
