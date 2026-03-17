<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="theme_ocean" />
    <!--! The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags !-->
    <!--! BEGIN: Apps Title-->
    <title>{{ config('app.name') ?? 'App'}} | Dashboard</title>
    <!--! END:  Apps Title-->
    <!--! BEGIN: Favicon-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/admin/images/favicon.ico') }}" />
    <!--! END: Favicon-->
    <!--! BEGIN: Bootstrap CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/bootstrap.min.css') }}" />
    <!--! END: Bootstrap CSS-->
    <!--! BEGIN: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/vendors/css/daterangepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/vendors/css/dataTables.bs5.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/sweetalert2/dist/sweetalert2.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/vendors/css/select2.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/vendors/css/select2-theme.min.css') }}" />
    <!--! END: Vendors CSS-->
    <!--! BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/theme.min.css') }}" />
    <!--! END: Custom CSS-->
    <!--! HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries !-->
    <!--! WARNING: Respond.js doesn"t work if you view the page via file: !-->
    @stack('styles')
</head>

<body>
    <!--! ================================================================ !-->
    <!--! [Start] Navigation Manu !-->
    <!--! ================================================================ !-->
    @include('layouts.admin.partials.sidebar')
    <!--! ================================================================ !-->
    <!--! [End]  Navigation Manu !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! [Start] Header !-->
    <!--! ================================================================ !-->
    @include('layouts.admin.partials.header')
    <!--! ================================================================ !-->
    <!--! [End] Header !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! [Start] Main Content !-->
    <!--! ================================================================ !-->
    <main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">{{$title ?? 'Dashboard'}}</h5>
                    </div>
                    <ul class="breadcrumb">
                        @php
                            $crumbs = $breadcrumbs ?? null;
                        @endphp
                        @if(is_array($crumbs) && count($crumbs))
                            @foreach($crumbs as $crumb)
                                @php
                                    $label = $crumb['label'] ?? null;
                                    $url = $crumb['url'] ?? null;
                                    $active = (bool) ($crumb['active'] ?? false);
                                @endphp
                                @continue(empty($label))
                                <li class="breadcrumb-item {{ $active ? 'active' : '' }}">
                                    @if($url && !$active)
                                        <a href="{{ $url }}">{{ $label }}</a>
                                    @else
                                        {{ $label }}
                                    @endif
                                </li>
                            @endforeach
                        @else
                            <li class="breadcrumb-item"><a href="/admin">Home</a></li>
                            <li class="breadcrumb-item">{{ $title ?? 'Dashboard' }}</li>
                        @endif
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                            @yield('header_content')
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ page-header ] end -->
            <!-- [ Main Content ] start -->
            <div class="main-content">
                <div class="row">
                    @yield('content')
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
        <!-- [ Footer ] start -->
        
        <!-- [ Footer ] end -->
    </main>
    <!--! ================================================================ !-->
    <!--! [End] Main Content !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! Footer Script !-->
    <!--! ================================================================ !-->
    <!--! BEGIN: Vendors JS !-->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/admin/vendors/js/vendors.min.js') }}"></script>
    <!-- vendors.min.js {always must need to be top} -->
    <script src="{{ asset('assets/admin/vendors/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/vendors/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/vendors/js/dataTables.bs5.min.js') }}"></script>
    <script src="{{ asset('assets/admin/vendors/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/vendors/js/circle-progress.min.js') }}"></script>
    <script src="{{ asset('assets/admin/vendors/js/select2.min.js') }}"></script>
    <!--! END: Vendors JS !-->
    <!--! BEGIN: Apps Init  !-->
    <script src="{{ asset('assets/admin/js/common-init.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/dashboard-init.min.js') }}"></script>
    <!--! END: Apps Init !-->
    <!--! BEGIN: Theme Customizer  !-->
    <script src="{{ asset('assets/admin/js/theme-customizer-init.min.js') }}"></script>
    <!--! END: Theme Customizer !-->
    @stack('scripts')
    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <x-admin.ui.sweetalert />
    <x-admin.table.delete-button />
</body>

</html>