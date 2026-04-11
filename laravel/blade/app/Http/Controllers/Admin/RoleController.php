<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\RoleDataTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends AdminController implements HasMiddleware
{
    public static function middleware(): array
    {
        return static::permissionsFor('roles');
    }

    public function index(RoleDataTable $dataTable)
    {
        return $dataTable->render('pages.admin.roles.index', [
            'title' => 'Role',
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.roles.form', [
            'title'       => 'Tambah Role',
            'roles' => Role::all()->pluck('name', 'id')->toArray(),
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create(['name' => $validated['name']]);
        $role->syncPermissions(
            Permission::whereIn('id', $validated['permissions'] ?? [])->get()
        );

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role): View
    {
        return view('pages.admin.roles.form', [
            'title'       => 'Edit Role',
            'role'        => $role->load('permissions'),
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255', "unique:roles,name,{$role->id}"],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions(
            Permission::whereIn('id', $validated['permissions'] ?? [])->get()
        );

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
