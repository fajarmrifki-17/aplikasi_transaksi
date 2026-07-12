<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('departments')->insert([
            ['name' => 'Information Technology', 'code' => 'IT', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Human Resources', 'code' => 'HR', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Finance & Accounting', 'code' => 'FIN', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Operations', 'code' => 'OPS', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marketing', 'code' => 'MKT', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
