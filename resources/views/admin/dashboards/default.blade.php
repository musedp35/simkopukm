@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col s12">
        <h4 class="center">Dashboard</h4>
        <p class="center">Selamat datang, <strong>{{ auth()->user()->USERNAME }}</strong>!</p>
    </div>
</div>
@endsection
