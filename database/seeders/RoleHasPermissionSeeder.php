<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleHasPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $acls = [
            // Owner Role
            [
                'permission_id' => 1,
                'role_id' => 1
            ],
            [
                'permission_id' => 2,
                'role_id' => 1
            ],
            [
                'permission_id' => 3,
                'role_id' => 1
            ],
            [
                'permission_id' => 4,
                'role_id' => 1
            ],
            // Owner Employee
            [
                'permission_id' => 5,
                'role_id' => 1
            ],
            [
                'permission_id' => 6,
                'role_id' => 1
            ],
            [
                'permission_id' => 7,
                'role_id' => 1
            ],
            [
                'permission_id' => 8,
                'role_id' => 1
            ],
            // Opertional Manager Role
            [
                'permission_id' => 1,
                'role_id' => 2
            ],
            [
                'permission_id' => 2,
                'role_id' => 2
            ],
            [
                'permission_id' => 3,
                'role_id' => 2
            ],
            [
                'permission_id' => 4,
                'role_id' => 2
            ],
            // Opertional Manager Employee
            [
                'permission_id' => 5,
                'role_id' => 2
            ],
            [
                'permission_id' => 6,
                'role_id' => 2
            ],
            [
                'permission_id' => 7,
                'role_id' => 2
            ],
            [
                'permission_id' => 8,
                'role_id' => 2
            ],
            // Opertional Manager Table
            [
                'permission_id' => 9,
                'role_id' => 2
            ],
            [
                'permission_id' => 10,
                'role_id' => 2
            ],
            [
                'permission_id' => 11,
                'role_id' => 2
            ],
            [
                'permission_id' => 12,
                'role_id' => 2
            ],
            // Opertional Manager Customer
            [
                'permission_id' => 13,
                'role_id' => 2
            ],
            [
                'permission_id' => 14,
                'role_id' => 2
            ],
            [
                'permission_id' => 15,
                'role_id' => 2
            ],
            [
                'permission_id' => 16,
                'role_id' => 2
            ],
            // Opertional Manager Reservation
            [
                'permission_id' => 17,
                'role_id' => 2
            ],
            [
                'permission_id' => 18,
                'role_id' => 2
            ],
            [
                'permission_id' => 19,
                'role_id' => 2
            ],
            [
                'permission_id' => 20,
                'role_id' => 2
            ],
            // Opertional Manager Order
            [
                'permission_id' => 21,
                'role_id' => 2
            ],
            [
                'permission_id' => 22,
                'role_id' => 2
            ],
            [
                'permission_id' => 23,
                'role_id' => 2
            ],
            [
                'permission_id' => 24,
                'role_id' => 2
            ],
            // Opertional Manager Transaction
            [
                'permission_id' => 25,
                'role_id' => 2
            ],
            [
                'permission_id' => 26,
                'role_id' => 2
            ],
            [
                'permission_id' => 27,
                'role_id' => 2
            ],
            [
                'permission_id' => 28,
                'role_id' => 2
            ],
            // Opertional Manager Menu
            [
                'permission_id' => 29,
                'role_id' => 2
            ],
            [
                'permission_id' => 30,
                'role_id' => 2
            ],
            [
                'permission_id' => 31,
                'role_id' => 2
            ],
            [
                'permission_id' => 32,
                'role_id' => 2
            ],
            // Opertional Manager Ingredient
            [
                'permission_id' => 33,
                'role_id' => 2
            ],
            [
                'permission_id' => 34,
                'role_id' => 2
            ],
            [
                'permission_id' => 35,
                'role_id' => 2
            ],
            [
                'permission_id' => 36,
                'role_id' => 2
            ],
            // Opertional Manager Stock
            [
                'permission_id' => 37,
                'role_id' => 2
            ],
            [
                'permission_id' => 38,
                'role_id' => 2
            ],
            [
                'permission_id' => 39,
                'role_id' => 2
            ],
            [
                'permission_id' => 40,
                'role_id' => 2
            ],
            // Opertional Manager Report
            [
                'permission_id' => 41,
                'role_id' => 2
            ],
            // Waiter Table
            [
                'permission_id' => 10,
                'role_id' => 3
            ],
            [
                'permission_id' => 11,
                'role_id' => 3
            ],
            // Waiter Customer
            [
                'permission_id' => 13,
                'role_id' => 3
            ],
            [
                'permission_id' => 14,
                'role_id' => 3
            ],
            [
                'permission_id' => 15,
                'role_id' => 3
            ],
            [
                'permission_id' => 16,
                'role_id' => 3
            ],
            // Waiter Reservation
            [
                'permission_id' => 17,
                'role_id' => 3
            ],
            [
                'permission_id' => 18,
                'role_id' => 3
            ],
            [
                'permission_id' => 19,
                'role_id' => 3
            ],
            [
                'permission_id' => 20,
                'role_id' => 3
            ],
            // Cashier Table
            [
                'permission_id' => 10,
                'role_id' => 4
            ],
            [
                'permission_id' => 11,
                'role_id' => 4
            ],
            // Cashier Customer
            [
                'permission_id' => 13,
                'role_id' => 4
            ],
            [
                'permission_id' => 14,
                'role_id' => 4
            ],
            [
                'permission_id' => 15,
                'role_id' => 4
            ],
            [
                'permission_id' => 16,
                'role_id' => 4
            ],
            // Cashier Reservation
            [
                'permission_id' => 17,
                'role_id' => 4
            ],
            [
                'permission_id' => 18,
                'role_id' => 4
            ],
            [
                'permission_id' => 19,
                'role_id' => 4
            ],
            [
                'permission_id' => 20,
                'role_id' => 4
            ],
        ];

        DB::table('role_has_permissions')->insert($acls);

    }
}
