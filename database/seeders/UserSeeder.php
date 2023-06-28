<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $isEmptyUsers = User::count() === 0;

        $userSA = User::updateOrCreate([
            'id' => 1
        ], [
            'name' => fake()->name(),
            'email' => 'superadmin@email.com',
            'email_verified_at' => now(),
            'password' => '123',
        ]);
        $userA = User::updateOrCreate([
            'id' => 2
        ], [
            'name' => fake()->name(),
            'created_by'=>1,
            'email' => 'admin@email.com',
            'email_verified_at' => now(),
            'password' => '123',
        ]);
        $userU = User::updateOrCreate([
            'id' => 3
        ], [
            'created_by'=>2,
            'name' => fake()->name(),
            'email' => 'user@email.com',
            'email_verified_at' => now(),
            'password' => '123',
        ]);

        $userSA->newSuperAdmin();
        $userA->newAdmin();
        $userU->newUser();

        !$isEmptyUsers ?: User::factory(97)->create()->each(function (User $user) {
            $role = fake()->randomElement(['super_admin', 'admin', 'user',]);
            $user->roles()->attach(Role::where('key', $role)->first());
            !($role !== 'user') ?: $user->created_by = 1;
            !($role === 'user') ?: $user->created_by = rand(1, 2);
            !$user->isDirty('created_by') ?: $user->save();
        });
    }
}
