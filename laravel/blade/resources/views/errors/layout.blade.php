<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') — @yield('title')</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/admin/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/theme.min.css') }}">
</head>
<body>
    <main class="auth-minimal-wrapper">
        <div class="auth-minimal-inner">
            <div class="minimal-card-wrapper">
                <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative">
                    <div class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-0 start-50">
                        <img src="{{ asset('assets/admin/images/logo-abbr.png') }}" alt="" class="img-fluid">
                    </div>
                    <div class="card-body p-sm-5 text-center">
                        @php $code = trim($__env->yieldContent('code')); @endphp
                        <h2 class="fw-bolder mb-4" style="font-size: 120px">{{ $code[0] ?? '' }}<span class="text-danger">{{ $code[1] ?? '' }}</span>{{ $code[2] ?? '' }}</h2>
                        <h4 class="fw-bold mb-2">@yield('title')</h4>
                        <p class="fs-12 fw-medium text-muted">@yield('message')</p>
                        <div class="mt-5">
                            <a href="{{ url('/') }}" class="btn btn-light-brand w-100">Back Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="{{ asset('assets/admin/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/common-init.min.js') }}"></script>
</body>
</html>
