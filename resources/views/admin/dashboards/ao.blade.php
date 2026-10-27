@extends('layouts.app')

@section('title', 'Dashboard AO')

@section('content')
<div class="row">
    <div class="col s12">
        <h4 class="center">Dashboard AO</h4>
        <p class="center">Selamat datang, <strong>{{ auth()->user()->USERNAME }}</strong>!</p>
    </div>
</div>
@endsection
