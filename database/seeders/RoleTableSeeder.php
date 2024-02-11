<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        
        $role1 = Role::firstOrCreate(
            ['name' => 'admin']
        );
        $role2 = Role::firstOrCreate(
            ['name' => 'player']
        );
        
        Permission::firstOrCreate(['name'=>'getGames'])->syncRoles([$role1,$role2]);

        Permission::firstOrCreate(['name'=>'deleteAllGames'])->syncRoles([$role1,$role2]);

        Permission::firstOrCreate(['name'=>'rollDice'])->syncRoles([$role1,$role2]);

        Permission::firstOrCreate(['name'=>'getAllPlayers'])->syncRoles([$role1,$role2]);

        Permission::firstOrCreate(['name'=>'getLoser'])->syncRoles([$role1,$role2]);
        
        Permission::firstOrCreate(['name'=>'getWinner'])->syncRoles([$role1,$role2]);

        Permission::firstOrCreate(['name'=>'update'])->syncRoles([$role1,$role2]);

        Permission::firstOrCreate(['name'=>'getRankingWithDetails'])->syncRoles([$role1,$role2]);
        
    }
}
