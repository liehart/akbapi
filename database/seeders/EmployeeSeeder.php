<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employees = [
            [
                'name' => 'Owner Account',
                'role_id' => 1,
                'phone' => '000000000000',
                'date_join' => now(),
                'locked' => true,
                'email' => 'owner@atmakoreanbbq.com',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Ops. Manager Account',
                'role_id' => 2,
                'phone' => '000000000000',
                'date_join' => now(),
                'locked' => true,
                'email' => 'opsman@atmakoreanbbq.com',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        DB::table('employees')->insert($employees);
    }
}
