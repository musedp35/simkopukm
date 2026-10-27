<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIMKOP') - Sistem Informasi Koperasi</title>

    {{-- CSS dari CI3 --}}
    <link href="{{ asset('assets/css/materialize.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/css/custom/custom-style.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/css/toastrv2.0.1.css') }}" type="text/css" rel="stylesheet">
    @yield('css')
</head>
<body>

    {{-- Header (navbar) --}}
    <nav class="blue lighten-1" role="navigation">
        <div class="nav-wrapper container">
            <a class="brand-logo" href="{{ route('admin.dashboard') }}">SIMKOP</a>
            <ul class="right hide-on-med-and-down">
                <li>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       style="cursor: pointer;">
                        Logout ({{ auth()->user()->USERNAME ?? '' }})
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    {{-- Form logout --}}
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    {{-- Flash message --}}
    @if (session('alert'))
        <div class="container">
            <div class="card red lighten-5 animated slideInDown" id="card-alert">
                <div class="card-content red-text" style="padding: 6px !important;">
                    <p>{{ session('alert') }}</p>
                </div>
            </div>
        </div>
        <script>
            setTimeout(function() { document.getElementById('card-alert')?.remove(); }, 3000);
        </script>
    @endif

    {{-- Content --}}
    <main class="container">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="page-footer blue lighten-1">
        <div class="container">
            &copy; {{ date('Y') }} Sistem Informasi Koperasi Setia Bhakti Wanita
        </div>
    </footer>

    {{-- JS dari CI3 --}}
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/materialize.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/jquery-1.11.2.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/chartist-js/chartist.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/sparkline/sparkline-script.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom-script.js') }}"></script>
    <script src="{{ asset('assets/js/toastrv2.0.1.js') }}"></script>

    @stack('scripts')
    @yield('js')
</body>
</html>
