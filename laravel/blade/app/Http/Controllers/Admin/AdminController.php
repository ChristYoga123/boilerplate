<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;

abstract class AdminController extends Controller
{
    /**
     * Build permission middleware for a resource controller.
     *
     * Default map (standard CRUD):
     *   index  → [index]          create → [create, store]
     *   edit   → [update, update]  delete → [destroy]
     *
     * $extra entries:
     *   'export'                     →  permission:export  only:[export]
     *   'import' => ['importForm', 'doImport']  →  custom methods
     *
     * Examples:
     *   static::permissionsFor('users')
     *   static::permissionsFor('users', ['export', 'import'])
     *   static::permissionsFor('users', ['import' => ['importForm', 'doImport']])
     *   static::permissionsFor('dashboard', ['view' => ['index']], withDefaults: false)
     */
    protected static function permissionsFor(string $resource, array $extra = [], bool $withDefaults = true): array
    {
        $defaults = $withDefaults ? [
            'index'        => ['index'],
            'create'       => ['create', 'store'],
            'edit'         => ['edit', 'update'],
            'delete'       => ['destroy'],
            'force_delete' => ['forceDestroy'],
            'restore'      => ['restore'],
            'import'       => ['import'],
            'export'       => ['export'],
        ] : [];

        $normalized = [];
        foreach ($extra as $key => $value) {
            is_int($key)
                ? $normalized[$value] = [$value]          // 'export' → ['export' => ['export']]
                : $normalized[$key]   = (array) $value;   // 'import' => ['a','b'] stays
        }

        $resolved = array_merge($defaults, $normalized);

        return array_map(
            fn (string $permission, array $methods) => new Middleware("permission:{$resource}.{$permission}", only: $methods),
            array_keys($resolved),
            $resolved,
        );
    }
}
