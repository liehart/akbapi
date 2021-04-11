<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeRoleSeeder extends Seeder
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
                'locked' => true,
                'created_at' => now(),
                'updated_at' => now()
            ], [
                'name' => 'Operational Manager',
                'locked' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        DB::table('employee_roles')->insert($roles);
    }
}
