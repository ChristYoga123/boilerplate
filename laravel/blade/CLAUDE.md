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
  - Auth-only: `GET /admin` (dashboard), `GET /admin/users` (resource), `GET /admin/roles` (resource), `POST /admin/logout`

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
- `app.blade.php` — authenticated admin layout; uses `@yield('content')` and `@stack('scripts')`/`@stack('styles')`
- `guest.blade.php` — unauthenticated layout (login page)
- `form.blade.php` — wraps a `<form>` and yields `form_content`; pass `action`, `method`, and optionally `multipart` (boolean) via `@extends`. Renders a default Save/Cancel footer unless `form_footer` section is defined.
- `partials/` — sidebar, sidebar-menu, header, footer

**Blade Components** (`resources/views/components/admin/`):

`ui/` — `button`, `breadcrumb`, `title`, `widget`, `sweetalert`, `modal`, `container`

`form/`:
- `text-input` — props: `name`, `type`, `label`, `placeholder`, `value`, `required`, `revealable` (toggle visibility for password fields)
- `textarea` — props: `name`, `label`, `placeholder`, `value`, `required`, `rows` (default 4)
- `rich-editor` — Quill 1.3.7 WYSIWYG; props: `name`, `label`, `value`, `required`, `placeholder`, `height` (px, default 250). Syncs to a hidden `<textarea>` for form submission. XSS-safe client-side (Quill Delta sanitizes input); **server-side**: strip with `strip_tags()` or `mews/purifier` before persisting. Quill JS/CSS loaded via CDN using `@stack('styles'/'scripts')`.
- `select2` — Select2-powered dropdown; props: `name`, `label`, `options` (assoc array `value => label`), `value`, `required`, `multiple`, `placeholder`
- `file-input` — file upload with image preview; props: `name`, `label`, `required`, `accept`, `preview`, `multiple`, `currentImages` (array of URLs for existing images)
- `checkbox` — single checkbox
- `section` — titled section wrapper for grouping form fields
- `grid` — responsive grid wrapper; prop: `cols` (number of columns)
- `repeater` — dynamic add/remove rows; props: `name`, `label`, `addLabel`, `minItems`, `maxItems`, `initialCount`. Child fields use **plain names** (`name="label"`) — JS auto-prefixes them as `repeaterName[index][label]`. Nested (`name="meta[key]"`) and multiple (`name="tags[]"`) names are handled. Select2 inside rows is auto-reinitialised. Rich-editor inside repeater is **not** supported (use `textarea` instead).

`table/`:
- `filters` — wrapper row for DataTable filter controls
- `select-filter` — column filter dropdown (Select2); props: `name`, `label`, `options`, `tableId` (without `#`), `column` (DataTable column index), `cols` (Bootstrap col width, default 3)
- `date-filter` — single date column filter
- `date-range-filter` — date range column filter
- `edit-button`, `delete-button` — action buttons for table rows
- `container` — table section wrapper

Components are referenced as `<x-admin.ui.sweetalert />`, `<x-admin.form.text-input />`, `<x-admin.table.select-filter />`, etc.

### DataTables Pattern

Server-side tables use **yajra/laravel-datatables**. Each resource has a dedicated DataTable class in `app/DataTables/Admin/`. The controller injects the DataTable and calls `$dataTable->render('view.path', $data)`. The view renders the table with `{!! $dataTable->table() !!}` and pushes scripts with `{!! $dataTable->scripts() !!}`.

### Permission Middleware Pattern

All admin resource controllers extend `AdminController` and implement `HasMiddleware`. Permissions are wired via the static helper:

```php
public static function middleware(): array
{
    return static::permissionsFor('users');
    // generates middleware for users.index, users.create, users.store, users.edit, users.update, users.destroy
}
```

`permissionsFor(string $resource, array $extra = [], bool $withDefaults = true)` — pass `$extra` to protect additional actions, or `$withDefaults = false` to skip the standard CRUD set.

### Roles & Permissions

Uses **spatie/laravel-permission**. The `User` model has the `HasRoles` trait. Permissions follow the `resource.action` convention (e.g., `users.index`, `users.create`). `RolePermissionSeeder` creates an `admin` role and seeds the default admin user (`admin@gmail.com` / `password`).

### Adding a New Admin Resource

Checklist when adding a new resource (e.g., `posts`):

1. **Controller** — extend `AdminController`, implement `HasMiddleware`, call `static::permissionsFor('posts')`
2. **DataTable** — create `app/DataTables/Admin/PostDataTable.php` extending `Yajra\DataTables\Services\DataTable`
3. **Routes** — add `Route::resource('posts', PostController::class)->except(['show'])` inside the `AuthMiddleware` group in `routes/admin.php`
4. **Views** — create `resources/views/pages/admin/posts/index.blade.php` and `form.blade.php`
5. **Permissions** — add the new permission slugs to `RolePermissionSeeder` and re-seed

### Language

UI strings are in **Indonesian** (e.g., "Pengguna" = Users, "Tambah" = Add, "Simpan" = Save, "Batal" = Cancel). Keep new UI text consistent with this.

### Asset Stack

- Admin UI assets are pre-built and served from `public/assets/admin/` (Bootstrap-based theme, DataTables, ApexCharts, SweetAlert2)
- `resources/css/app.css` + `resources/js/app.js` are compiled by **Vite** with **Tailwind CSS v4** (for front-end/custom pages)
- SweetAlert2 is also available via npm (`sweetalert2` package) and via the `<x-admin.ui.sweetalert />` component which flashes session messages automatically

### Flash Messages

Success/error messages are passed via Laravel session (`with('success', ...)`, `with('error', ...)`) and displayed automatically by the `<x-admin.ui.sweetalert />` component included in the admin layout.
