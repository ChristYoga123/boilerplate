# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

### Initial Setup
```bash
composer run setup
```
This installs dependencies, copies `.env.example` to `.env`, generates an app key, runs migrations, installs npm packages, and builds assets.

### Development (all services concurrently)
```bash
composer run dev
```
Runs the Laravel dev server, queue listener, Pail log viewer, and Vite dev server together.

### Build assets
```bash
npm run build
```

### Testing
```bash
composer run test        # runs config:clear then php artisan test
php artisan test --filter TestClassName   # run a single test
```

### Code Style
```bash
./vendor/bin/pint        # Laravel Pint (PHP CS Fixer)
```

### Database
```bash
php artisan migrate
php artisan db:seed                        # seeds roles, permissions, and admin user
php artisan db:seed --class=RolePermissionSeeder
```

---

## Architecture Overview

This is a **Laravel 12 + Blade** admin panel boilerplate with a clear admin/front separation.

### Route Structure
- `routes/web.php` — public routes; includes `routes/admin.php`
- `routes/admin.php` — all admin routes under `/admin` prefix with `admin.` name prefix
  - Guest-only: `GET/POST /admin/login`
  - Auth-only: `GET /admin` (dashboard), `GET /admin/users`, `POST /admin/logout`

### Directory Conventions

Controllers, views, DataTables, and middleware are all namespaced under `Admin/` or `Front/`:

| Layer | Admin | Front |
|-------|-------|-------|
| Controllers | `app/Http/Controllers/Admin/` | `app/Http/Controllers/Front/` |
| Middleware | `app/Http/Middleware/Admin/` | — |
| DataTables | `app/DataTables/Admin/` | — |
| Blade pages | `resources/views/pages/admin/` | `resources/views/pages/front/` |
| Layouts | `resources/views/layouts/admin/` | `resources/views/layouts/front/` |
| Components | `resources/views/components/admin/` | `resources/views/components/front/` |

### View Layers

**Layouts** (`resources/views/layouts/admin/`):
- `app.blade.php` — authenticated admin layout (sidebar, header, main content area); uses `@yield('content')` and `@stack('scripts')`/`@stack('styles')`
- `guest.blade.php` — unauthenticated layout (login page)
- `form.blade.php` — form-focused layout
- `partials/` — sidebar, sidebar-menu, header, footer

**Blade Components** (`resources/views/components/admin/`):
- `ui/` — `button`, `breadcrumb`, `title`, `widget`, `sweetalert`
- `form/` — `text-input`, `checkbox`
- `table/` — `button`, `datatable`

Components are referenced as `<x-admin.ui.sweetalert />`, `<x-admin.form.text-input />`, etc.

### DataTables Pattern

Server-side tables use **yajra/laravel-datatables**. Each resource has a dedicated DataTable class in `app/DataTables/Admin/`. The controller injects the DataTable and calls `$dataTable->render('view.path', $data)`. The view renders the table with `{!! $dataTable->table() !!}` and pushes scripts with `{!! $dataTable->scripts() !!}`.

### Roles & Permissions

Uses **spatie/laravel-permission**. The `User` model has the `HasRoles` trait. Permissions follow the `resource.action` convention (e.g., `users.index`, `users.create`). `RolePermissionSeeder` creates an `admin` role and seeds the default admin user (`admin@gmail.com` / `password`).

### Asset Stack

- Admin UI assets are pre-built and served from `public/assets/admin/` (Bootstrap-based theme, DataTables, ApexCharts, SweetAlert2)
- `resources/css/app.css` + `resources/js/app.js` are compiled by **Vite** with **Tailwind CSS v4** (for front-end/custom pages)
- SweetAlert2 is also available via npm (`sweetalert2` package) and via the `<x-admin.ui.sweetalert />` component which flashes session messages automatically

### Flash Messages

Success/error messages are passed via Laravel session (`with('success', ...)`, `with('error', ...)`) and displayed automatically by the `<x-admin.ui.sweetalert />` component included in the admin layout.
