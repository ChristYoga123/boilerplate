<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Create Roles
         */
        $adminRole = Role::findOrCreate('admin');
        // ... register other roles here

        /**
         * Create Permissions by Resources
         */
        $this->createPermissionsByResources('dashboard', ['index']);
        $this->createPermissionsByResources('users', ['*']);
        $this->createPermissionsByResources('roles', ['*']);
        // ... register other permissions by resources here

        /**
         * Create User
         */
        $admin = User::query()
            ->firstOrCreate([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
            ], [
                'password' => Hash::make('password'),
            ]);

        $admin->assignRole($adminRole);

        /**
         * Assign Permissions to Admin Role
         */
        $adminRole->givePermissionTo(Permission::all());
    }

    private function createPermissionsByResources(string $resource, array $permissions = ['*']): void
    {
        // default permission set
        $default = ['index', 'create', 'edit', 'delete', 'force_delete', 'restore', 'import', 'export'];

        // if '*' is present in the array, merge default + custom (without the '*')
        if (in_array('*', $permissions, true)) {
            $custom = array_values(array_filter($permissions, fn ($p) => $p !== '*'));
            $toCreate = array_values(array_unique(array_merge($default, $custom)));
        } else {
            // otherwise, use only the explicit permissions provided
            $toCreate = $permissions;
        }

        foreach ($toCreate as $permission) {
            if (! is_string($permission) || $permission === '') {
                continue;
            }

            Permission::findOrCreate("{$resource}.{$permission}");
        }
    }
}
