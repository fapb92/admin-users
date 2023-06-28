<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->addRole(
            ['key' => 'super_admin'],
            ['name' => 'Super Administrador', 'priority' => 1],
            ['view_all_roles', 'view_all_permissions', 'view_rol_details', 'assign_roles', 'remove_roles', 'create_users', 'update_users']
        );

        $this->addRole(
            ['key' => 'admin'],
            ['name' => 'Administrador', 'priority' => 2],
            ['view_all_roles', 'view_rol_details', 'assign_roles', 'remove_roles', 'create_users', 'update_users']
        );

        $this->addRole(
            ['key' => 'user'],
            ['name' => 'User', 'priority' => 3],
            []
        );
    }

    protected function addRole($key = [], $toUpdate, $toAttach = [])
    {
        $role = Role::updateOrCreate(
            $key,
            $toUpdate
        );

        $role->permissions()->sync(Permission::whereIn('key', $toAttach)->get());
    }
}
