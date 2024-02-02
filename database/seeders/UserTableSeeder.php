<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web']
        );
       
        $role1 = Role::firstOrCreate(
            ['name' => 'player', 'guard_name' => 'web']
        );

        $user1 = User::firstOrCreate(
            [
                'name' => 'admin',
                'email' => 'admin@admin.com',
                'email_verified_at' => now(), 'password' => Hash::make('admin'),
                'remember_token' => Str::random(10),
            ]
        );
        if (!$user1->hasRole('admin')) {
            $user1->assignRole($role);
        }

        $user2 = User::firstOrCreate([
            'name' => 'player',
            'email' => 'player@player.com',
            'email_verified_at' => now(),
            'password' => Hash::make('player'),
            'remember_token' => Str::random(10),
        ]);
        if (!$user2->hasRole('player')) {
            $user2->assignRole($role1);
        }  

        User::factory()->count(10)->create();
    }
}
