<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\UserDataTable;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends AdminController implements HasMiddleware
{
    public static function middleware(): array
    {
        return static::permissionsFor('users');
    }

    public function index(UserDataTable $dataTable)
    {
        $nameOptions = User::query()
            ->orderBy('name')
            ->pluck('name', 'name')
            ->toArray();

        $roleOptions = Role::pluck('name', 'id')->toArray();

        return $dataTable->render('pages.admin.users.index', [
            'title'       => 'Pengguna',
            'nameOptions' => $nameOptions,
            'roleOptions' => $roleOptions,
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.users.form', [
            'title' => 'Tambah Pengguna',
            'roles' => Role::pluck('name', 'id')->toArray(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:dns', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        // Assign to Roles
        $user->assignRole(array_map('intval', $validated['roles']));

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('pages.admin.users.form', [
            'title' => 'Edit Pengguna',
            'user' => $user,
            'roles' => Role::pluck('name', 'id')->toArray(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:dns', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // Sync Roles
        $user->syncRoles(array_map('intval', $validated['roles']));

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
