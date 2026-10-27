@extends('layouts.app')

@section('content')
    <div class="row">
        {{-- Sidebar --}}
        <div class="col s12 m3">
            @include('admin.partials.sidebar')
        </div>

        {{-- Main content --}}
        <div class="col s12 m9">
            {{-- Flash success --}}
            @if (session('alert-success'))
                <div class="card green lighten-5" id="card-success">
                    <div class="card-content green-text">
                        <p>{{ session('alert-success') }}</p>
                    </div>
                </div>
                <script>
                    setTimeout(function() { document.getElementById('card-success')?.remove(); }, 3000);
                </script>
            @endif

            @if (session('alert'))
                <div class="card red lighten-5" id="card-error">
                    <div class="card-content red-text">
                        <p>{{ session('alert') }}</p>
                    </div>
                </div>
                <script>
                    setTimeout(function() { document.getElementById('card-error')?.remove(); }, 3000);
                </script>
            @endif

            @yield('admin-content')
        </div>
    </div>
@stop
