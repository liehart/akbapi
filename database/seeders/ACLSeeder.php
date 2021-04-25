<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ACLSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * 0: No Access
         * 1: Read
         * 2: Read, Modify
         * 3: Read, Create
         * 4: Read, Create, Modify
         * 5: Read, Create, Modify, Delete
         */
        $acls = [
            // role
            [
                'object' => 'role',
                'operation' => 5,
                'role_id' => 1, // owner
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'role',
                'operation' => 5,
                'role_id' => 2, // operational manager
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'role',
                'operation' => 1,
                'role_id' => 3, // waiter
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'role',
                'operation' => 1,
                'role_id' => 4, // cashier
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'role',
                'operation' => 1,
                'role_id' => 5, // chef
                'created_at' => now(),
                'updated_at' => now()
            ],
            // employee
            [
                'object' => 'employee',
                'operation' => 5,
                'role_id' => 1, // owner
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'employee',
                'operation' => 5,
                'role_id' => 2, // operational manager
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'employee',
                'operation' => 1,
                'role_id' => 3, // waiter
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'employee',
                'operation' => 1,
                'role_id' => 4, // cashier
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'employee',
                'operation' => 1,
                'role_id' => 5, // chef
                'created_at' => now(),
                'updated_at' => now()
            ],
            // table
            [
                'object' => 'table',
                'operation' => 5,
                'role_id' => 1, // owner
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'table',
                'operation' => 5,
                'role_id' => 2, // operational manager
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'table',
                'operation' => 2,
                'role_id' => 3, // waiter
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'table',
                'operation' => 2,
                'role_id' => 4, // cashier
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'table',
                'operation' => 1,
                'role_id' => 5, // chef
                'created_at' => now(),
                'updated_at' => now()
            ],
            // customer
            [
                'object' => 'customer',
                'operation' => 1,
                'role_id' => 1, // owner
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'customer',
                'operation' => 5,
                'role_id' => 2, // operational manager
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'customer',
                'operation' => 5,
                'role_id' => 3, // waiter
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'customer',
                'operation' => 5,
                'role_id' => 4, // cashier
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'customer',
                'operation' => 1,
                'role_id' => 5, // chef
                'created_at' => now(),
                'updated_at' => now()
            ],
            // reservation
            [
                'object' => 'reservation',
                'operation' => 1,
                'role_id' => 1, // owner
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'reservation',
                'operation' => 5,
                'role_id' => 2, // operational manager
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'reservation',
                'operation' => 5,
                'role_id' => 3, // waiter
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'reservation',
                'operation' => 5,
                'role_id' => 4, // cashier
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'reservation',
                'operation' => 1,
                'role_id' => 5, // chef
                'created_at' => now(),
                'updated_at' => now()
            ],
            // order
            [
                'object' => 'order',
                'operation' => 1,
                'role_id' => 1, // owner
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'order',
                'operation' => 5,
                'role_id' => 2, // operational manager
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'order',
                'operation' => 4,
                'role_id' => 3, // waiter
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'order',
                'operation' => 4,
                'role_id' => 4, // cashier
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'order',
                'operation' => 2,
                'role_id' => 5, // chef
                'created_at' => now(),
                'updated_at' => now()
            ],
            // transaction
            [
                'object' => 'transaction',
                'operation' => 1,
                'role_id' => 1, // owner
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'transaction',
                'operation' => 5,
                'role_id' => 2, // operational manager
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'transaction',
                'operation' => 1,
                'role_id' => 3, // waiter
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'transaction',
                'operation' => 3,
                'role_id' => 4, // cashier
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'transaction',
                'operation' => 1,
                'role_id' => 5, // chef
                'created_at' => now(),
                'updated_at' => now()
            ],
            // menu
            [
                'object' => 'menu',
                'operation' => 1,
                'role_id' => 1, // owner
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'menu',
                'operation' => 5,
                'role_id' => 2, // operational manager
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'menu',
                'operation' => 1,
                'role_id' => 3, // waiter
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'menu',
                'operation' => 1,
                'role_id' => 4, // cashier
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'menu',
                'operation' => 5,
                'role_id' => 5, // chef
                'created_at' => now(),
                'updated_at' => now()
            ],
            // ingredient
            [
                'object' => 'ingredient',
                'operation' => 1,
                'role_id' => 1, // owner
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'ingredient',
                'operation' => 5,
                'role_id' => 2, // operational manager
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'ingredient',
                'operation' => 1,
                'role_id' => 3, // waiter
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'ingredient',
                'operation' => 1,
                'role_id' => 4, // cashier
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'ingredient',
                'operation' => 5,
                'role_id' => 5, // chef
                'created_at' => now(),
                'updated_at' => now()
            ],
            // history
            [
                'object' => 'history',
                'operation' => 1,
                'role_id' => 1, // owner
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'history',
                'operation' => 5,
                'role_id' => 2, // operational manager
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'history',
                'operation' => 1,
                'role_id' => 3, // waiter
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'history',
                'operation' => 1,
                'role_id' => 4, // cashier
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'history',
                'operation' => 5,
                'role_id' => 5, // chef
                'created_at' => now(),
                'updated_at' => now()
            ],
            // report
            [
                'object' => 'report',
                'operation' => 1,
                'role_id' => 1, // owner
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'report',
                'operation' => 1,
                'role_id' => 2, // operational manager
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'report',
                'operation' => 1,
                'role_id' => 3, // waiter
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'report',
                'operation' => 1,
                'role_id' => 4, // cashier
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'object' => 'report',
                'operation' => 1,
                'role_id' => 5, // chef
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];
        DB::table('a_c_l_s')->insert($acls);
    }
}
