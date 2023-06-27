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
            ['key' => 'view_all_roles', 'skey'=>'p-001', 'name' => 'Ver todos los roles'],
            ['key' => 'view_all_permissions', 'skey'=>'p-002', 'name' => 'Ver todos los permisos'],
        ], ['key'], ['name']);
    }
}
