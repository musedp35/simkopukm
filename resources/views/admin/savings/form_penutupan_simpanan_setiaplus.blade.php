@extends('admin.layouts')
@section('title', 'Penutupan Simpanan SetiaPlus - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    <h5 class="blue-text" style="font-size:1.5rem;">Penutupan Simpanan SetiaPlus</h5>
    <a href="{{ route('admin.dashboard') }}" class="grey-text" style="display:block;margin-bottom:1rem;">
        <i class="material-icons" style="vertical-align:middle;">arrow_back</i> Kembali
    </a>
    <p class="grey-text">Form penutupan SetiaPlus lengkap (bilyet, saldo akhir, dsb.) menyusul di batch berikutnya.</p>
</div></div>
@stop
