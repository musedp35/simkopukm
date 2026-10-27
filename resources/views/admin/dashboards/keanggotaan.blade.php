@extends('layouts.app')

@section('title', 'Dashboard Keanggotaan')

@section('content')
<div class="row">
    <div class="col s12 m4">
        <div class="card blue lighten-1">
            <div class="card-content white-text">
                <span class="card-title">Total Anggota</span>
                <p class="display4">{{ $data['detailTotAgtDashboard']['totAgt'] ?? 0 }}</p>
            </div>
        </div>
    </div>
    <div class="col s12 m4">
        <div class="card green lighten-1">
            <div class="card-content white-text">
                <span class="card-title">Anggota Baru</span>
                <p class="display4">{{ $data['detailTotNewAgtDashboard']['totNewAgt'] ?? 0 }}</p>
            </div>
        </div>
    </div>
    <div class="col s12 m4">
        <div class="card orange lighten-1">
            <div class="card-content white-text">
                <span class="card-title">Calon Anggota</span>
                <p class="display4">{{ $data['detailTotCalonAgtDashboard']['totCalonAgt'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
