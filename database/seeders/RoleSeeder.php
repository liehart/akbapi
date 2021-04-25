<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'Owner',
                'slug' => 'owner',
                'locked' => true,
                'created_at' => now(),
                'updated_at' => now()
            ], [
                'name' => 'Operational Manager',
                'slug' => 'operational-manager',
                'locked' => true,
                'created_at' => now(),
                'updated_at' => now()
            ], [
                'name' => 'Waiter',
                'slug' => 'waiter',
                'locked' => true,
                'created_at' => now(),
                'updated_at' => now()
            ], [
                'name' => 'Cashier',
                'slug' => 'cashier',
                'locked' => true,
                'created_at' => now(),
                'updated_at' => now()
            ], [
                'name' => 'Chef',
                'slug' => 'chef',
                'locked' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        DB::table('roles')->insert($roles);
    }
}
