<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(3)->create()
            ->each(function($user) {
                $user->role()->save( \App\Models\Role::factory(App\Role::class)->make());
            });
    }
}
