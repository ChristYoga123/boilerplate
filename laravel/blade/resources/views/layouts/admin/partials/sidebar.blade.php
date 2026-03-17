@php
    $menuItems = config('admin_menus', []);
@endphp
<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('admin.dashboard') }}" class="b-brand">
                <img src="{{ asset('assets/admin/images/logo-full.png') }}" alt="" class="logo logo-lg" />
                <img src="{{ asset('assets/admin/images/logo-abbr.png') }}" alt="" class="logo logo-sm" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">
                <li class="nxl-item nxl-caption">
                    <label>Navigation</label>
                </li>
                @include('layouts.admin.partials.sidebar-menu', ['items' => $menuItems])
            </ul>
        </div>
    </div>
</nav>
