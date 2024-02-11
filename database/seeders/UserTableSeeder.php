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


        $user1 = User::firstOrCreate(
            [
                'name' => 'admin',
                'email' => 'admin@admin.com',
                'email_verified_at' => now(), 'password' => Hash::make('admin'),
                'remember_token' => Str::random(10),
            ]
        );
     
            $user1->assignRole('admin');
        

        $user2 = User::firstOrCreate([
            'name' => 'player',
            'email' => 'player@player.com',
            'email_verified_at' => now(),
            'password' => Hash::make('player'),
            'remember_token' => Str::random(10),
        ]);
    
            $user2->assignRole('player');
      

        User::factory()->count(10)->create();
    }
}
