@extends('admin.layouts')
@section('title', 'Pembukaan Simpanan SetiaPlus - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Pembukaan Simpanan SetiaPlus</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>

    <h6 class="blue-text">Pilih Anggota</h6>
    <form method="GET" action="{{ route('admin.savings.setiaplus') }}">
        <div class="row">
            <div class="col s12 m6"><div class="input-field">
                <input type="text" name="idHiddenSetPlus" value="{{ request('idHiddenSetPlus') }}" required>
                <label>No. Anggota / No. KTP / No. Akun</label>
            </div></div>
            <div class="col s12 m6">
                <button type="submit" class="btn blue"><i class="material-icons left">search</i> Cari</button>
            </div>
        </div>
    </form>

    @if($detailSetPlus)
    @php $d = $detailSetPlus; @endphp
    <hr>
    <h6 class="blue-text">Data Anggota</h6>
    <div class="table-responsive">
        <table class="highlight">
            <tbody>
                <tr><th>Nama</th><td>{{ $d['MEMBER_NAME'] ?? '-' }}</td></tr>
                <tr><th>No. Anggota</th><td>{{ $d['MEMBER_NO'] ?? '-' }}</td></tr>
                <tr><th>No. Account SetiaPlus</th><td><strong>{{ $NO_ACC }}</strong></td></tr>
            </tbody>
        </table>
    </div>
    <p class="grey-text">Form pembukaan SetiaPlus lengkap (bank jasa, jangka waktu, dll.) menyusul di batch berikutnya.</p>
    @else
    <p class="grey-text" style="margin-top:1rem;">Masukkan no. anggota / KTP untuk melihat form pembukaan SetiaPlus.</p>
    @endif
</div></div>
@stop
