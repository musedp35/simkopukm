@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="row">
    <div class="col s12">
        <h4 class="center">Dashboard Kasir</h4>
        <p class="center">Selamat datang, <strong>{{ auth()->user()->USERNAME }}</strong>!</p>
    </div>
</div>
@endsection
