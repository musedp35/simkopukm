@extends('admin.layouts')
@section('title', 'Manajemen Konstanta - SIMKOP')
@section('admin-content')
<div class="card"><div class="card-content">
    @if(session('alert-success'))
        <div class="card green lighten-5"><div class="card-content green-text">{{ session('alert-success') }}</div></div>
    @endif
    <h5 class="blue-text" style="font-size:1.5rem;margin-bottom:1rem;">Manajemen Konstanta</h5>
    <p class="grey-text" style="margin-bottom:1.5rem;">Kelola konstanta simpanan (jasa, pajak, limit) dan konstanta pinjaman untuk sistem KOPUM.</p>

    <div class="row">
        <div class="col s12 m4">
            <a href="{{ route('admin.constants.simhar') }}" class="btn blue waves-effect waves-light">
                <i class="material-icons left">account_balance</i> Konstanta SimHar
            </a>
        </div>
        <div class="col s12 m4">
            <a href="{{ route('admin.constants.ssb') }}" class="btn teal waves-effect waves-light">
                <i class="material-icons left">savings</i> Konstanta SSB
            </a>
        </div>
        <div class="col s12 m4">
            <a href="{{ route('admin.constants.pinjaman') }}" class="btn orange waves-effect waves-light">
                <i class="material-icons left">local_atm</i> Konstanta Pinjaman
            </a>
        </div>
    </div>

    <h6 class="blue-text" style="margin:1.5rem 0 0.5rem;">Log Konstanta</h6>
    <div class="row">
        <div class="col s12 m4">
            <a href="{{ route('admin.constants.simhar') }}#log-simhar" class="chip blue white-text">Log SimHar</a>
        </div>
        <div class="col s12 m4">
            <a href="{{ route('admin.constants.ssb') }}#log-ssb" class="chip teal white-text">Log SSB</a>
        </div>
        <div class="col s12 m4">
            <a href="{{ route('admin.constants.pinjaman') }}#log-pinjaman" class="chip orange white-text">Log Pinjaman</a>
        </div>
    </div>
</div></div>
@stop
