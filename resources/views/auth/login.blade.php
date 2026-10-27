<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - SIMKOP - Sistem Informasi Koperasi</title>

    {{-- CSS dari CI3 (asset path di Laravel) --}}
    <link href="{{ asset('assets/css/materialize.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/css/custom/custom-style.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/css/layouts/page-center.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('assets/css/toastrv2.0.1.css') }}" type="text/css" rel="stylesheet">

    <style>
        .input-field input[type=text]:focus + label,
        .input-field input[type=password]:focus + label {
            color: #07BC90 !important;
        }
        .btn { background-color: #1993AF; }
        .btn:hover { background-color: #1964AF; }
    </style>
</head>
<body style="background-image: url('bg2.png'); background-size: cover; background-attachment: fixed;">

    {{-- Cek pesan notifikasi (flashdata) --}}
    @if (session('alert') === 'error-login')
        <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
        <script src="{{ asset('assets/js/toastrv2.0.1.js') }}"></script>
        <script>
            toastr.options = {
                "closeButton": false, "newestOnTop": true, "progressBar": true,
                "positionClass": "toast-bottom-center", "preventDuplicates": false,
                "showDuration": 1000, "hideDuration": 1000, "timeOut": 1000,
                "extendedTimeOut": 1000
            };
            toastr.error('Nama dan Sandi salah');
        </script>
    @endif

    <br/><br/>
    <div id="login-page" class="row">
        <div class="col s12 z-depth-4 card-panel">
            <form class="login-form" action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="input-field col s12 center">
                        <img src="{{ asset('assets/images/favicon/sbw_logo.png') }}" alt=""
                             class="responsive-img valign profile-image-login">
                        <p class="center login-form-text">Sistem Informasi Koperasi Setia Bhakti Wanita</p>
                    </div>
                </div>

                <div class="row margin">
                    <div class="input-field col s12">
                        <i class="mdi-social-person-outline prefix"></i>
                        <input id="username" type="text" name="username" required>
                        <label for="username" class="center-align">Nama Pengguna</label>
                    </div>
                </div>

                <div class="row margin">
                    <div class="input-field col s12">
                        <i class="mdi-action-lock-outline prefix"></i>
                        <input id="password" type="password" name="password" required>
                        <label for="password">Kata Sandi</label>
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s12">
                        <button type="submit" id="btlogin" name="login"
                                class="waves-effect waves-light btn card-panel col s12">
                            Login
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- JS dari CI3 --}}
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/materialize.min.js') }}"></script>
    <script src="{{ asset('assets/js/toastrv2.0.1.js') }}"></script>

    <script>
        $('#btlogin').click(function() {
            if ($('#username').val() == '' && $('#password').val() == '') {
                toastr.info('Anda Harus Login Dahulu');
            }
        });
    </script>
</body>
</html>
