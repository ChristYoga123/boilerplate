@php
    use Illuminate\Support\Arr;
    use Illuminate\Support\Facades\Gate;
    use Illuminate\Support\Str;

    if (! function_exists('admin_menu_collect_permissions')) {
        function admin_menu_collect_permissions(array $item): array
        {
            $perms = (array) ($item['can'] ?? []);

            foreach ($item['children'] ?? [] as $child) {
                $perms = array_merge($perms, admin_menu_collect_permissions($child));
            }

            return array_values(array_unique(Arr::flatten($perms)));
        }
    }

    if (! function_exists('admin_menu_is_active')) {
        function admin_menu_is_active(array $item): bool
        {
            $patterns = (array) ($item['active'] ?? []);

            if (isset($item['route'])) {
                $patterns[] = $item['route'];

                // Auto-expand resource routes:
                // admin.roles.index => admin.roles.*
                // admin.users.index => admin.users.*
                $routeName = (string) $item['route'];
                if (Str::endsWith($routeName, '.index')) {
                    $patterns[] = Str::beforeLast($routeName, '.index') . '.*';
                }
            }

            $patterns = array_filter($patterns);

            foreach ($patterns as $pattern) {
                if (request()->routeIs($pattern)) {
                    return true;
                }
            }

            foreach ($item['children'] ?? [] as $child) {
                if (admin_menu_is_active($child)) {
                    return true;
                }
            }

            return false;
        }
    }

    $sidebarUser = auth()->user();
@endphp

@foreach ($items as $item)
    @php
        $hasChildren = ! empty($item['children'] ?? []);
        $url = isset($item['route']) ? route($item['route']) : ($item['url'] ?? 'javascript:void(0);');
        $allPermissions = admin_menu_collect_permissions($item);
        $can = empty($allPermissions)
            || ($sidebarUser && collect($allPermissions)->contains(fn ($p) => Gate::forUser($sidebarUser)->allows($p)));
        $isActive = admin_menu_is_active($item);
    @endphp
    @if ($can)
        <li class="nxl-item {{ $hasChildren ? 'nxl-hasmenu' : '' }} {{ $isActive ? 'active' : '' }} {{ ($hasChildren && $isActive) ? 'nxl-trigger' : '' }}">
            <a href="{{ $hasChildren ? 'javascript:void(0);' : $url }}" class="nxl-link {{ $isActive ? 'active' : '' }}">
                @if (! empty($item['icon'] ?? null))
                    <span class="nxl-micon"><i class="{{ $item['icon'] }}"></i></span>
                @endif
                <span class="nxl-mtext">{{ $item['title'] }}</span>
                @if ($hasChildren)
                    <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                @endif
            </a>
            @if ($hasChildren)
                <ul class="nxl-submenu">
                    @include('layouts.admin.partials.sidebar-menu', ['items' => $item['children']])
                </ul>
            @endif
        </li>
    @endif
@endforeach
