<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::upsert([
            ['key' => 'view_all_roles', 'skey' => 'p-001', 'name' => 'Ver todos los roles'],
            ['key' => 'view_rol_details', 'skey' => 'p-002', 'name' => 'ver detalles de los roles'],
            ['key' => 'view_all_permissions', 'skey' => 'p-003', 'name' => 'Ver todos los permisos'],
            ['key' => 'assign_roles', 'skey' => 'p-004', 'name' => 'Asignar roles'],
            ['key' => 'remove_roles', 'skey' => 'p-005', 'name' => 'Remover roles'],
            ['key' => 'create_users', 'skey' => 'p-006', 'name' => 'Crear usuarios'],
            ['key' => 'update_users', 'skey' => 'p-007', 'name' => 'Actualizar usuarios'],
            ['key' => 'erase_users', 'skey' => 'p-008', 'name' => 'Borrar usuarios'],
            ['key' => 'view_all_user', 'skey' => 'p-009', 'name' => 'Ver todos los usuarios'],
            ['key' => 'view_user_details', 'skey' => 'p-010', 'name' => 'Ver información de usuario'],
        ], ['key'], ['skey', 'name']);
    }
}
