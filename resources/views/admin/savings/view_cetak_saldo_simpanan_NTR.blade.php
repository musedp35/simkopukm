@extends('admin.layouts')
@section('title', 'Cetak Saldo NTR - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Saldo Simpanan NTR</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>
    <p class="grey-text">Laporan saldo NTR — lengkap menyusul di batch berikutnya.</p>
</div></div>
@stop
