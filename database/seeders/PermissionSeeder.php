<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [

            // Employee Role
            [
                'name' => 'role.create',
                'label' => 'Create Employee Role',
                'description' => 'Allow user to add new employee role'
            ],
            [
                'name' => 'role.read',
                'label' => 'Show Employee Role',
                'description' => 'Allow user to read employee role data'
            ],
            [
                'name' => 'role.update',
                'label' => 'Update Employee Role',
                'description' => 'Allow user to update employee role data'
            ],
            [
                'name' => 'role.delete',
                'label' => 'Delete Employee Role',
                'description' => 'Allow user to delete employee role data'
            ],

            // Employee
            [
                'name' => 'employee.create',
                'label' => 'Create Employee',
                'description' => 'Allow user to add new employee'
            ],
            [
                'name' => 'employee.read',
                'label' => 'Show Employee',
                'description' => 'Allow user to read employee data'
            ],
            [
                'name' => 'employee.update',
                'label' => 'Update Employee',
                'description' => 'Allow user to update employee data'
            ],
            [
                'name' => 'employee.delete',
                'label' => 'Delete Employee',
                'description' => 'Allow user to delete employee data'
            ],

            // Table
            [
                'name' => 'table.create',
                'label' => 'Create Table',
                'description' => 'Allow user to add new table'
            ],
            [
                'name' => 'table.read',
                'label' => 'Show Table',
                'description' => 'Allow user to read table data'
            ],
            [
                'name' => 'table.update',
                'label' => 'Update Table',
                'description' => 'Allow user to update table data'
            ],
            [
                'name' => 'table.delete',
                'label' => 'Delete Table',
                'description' => 'Allow user to delete table data'
            ],

            // Customer
            [
                'name' => 'customer.create',
                'label' => 'Create Customer',
                'description' => 'Allow user to add new customer'
            ],
            [
                'name' => 'customer.read',
                'label' => 'Show Customer',
                'description' => 'Allow user to read customer data'
            ],
            [
                'name' => 'customer.update',
                'label' => 'Update Customer',
                'description' => 'Allow user to update customer data'
            ],
            [
                'name' => 'customer.delete',
                'label' => 'Delete Customer',
                'description' => 'Allow user to delete customer data'
            ],

            // Reservation
            [
                'name' => 'reservation.create',
                'label' => 'Create Reservation',
                'description' => 'Allow user to add new reservation'
            ],
            [
                'name' => 'reservation.read',
                'label' => 'Show Reservation',
                'description' => 'Allow user to read reservation data'
            ],
            [
                'name' => 'reservation.update',
                'label' => 'Update Reservation',
                'description' => 'Allow user to update reservation data'
            ],
            [
                'name' => 'reservation.delete',
                'label' => 'Delete Reservation',
                'description' => 'Allow user to delete reservation data'
            ],
        ];

        DB::table('permissions')->insert($permissions);
    }
}
