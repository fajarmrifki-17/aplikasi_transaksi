<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('expense_categories')->insert([
            ['name' => 'Office Supplies', 'code' => 'OFF-SUP', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Travel & Transportation', 'code' => 'TRV-TRA', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Utilities & Internet', 'code' => 'UTL-INT', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Software & Subscriptions', 'code' => 'SFT-SUB', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hardware & Equipment', 'code' => 'HDW-EQP', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marketing & Advertising', 'code' => 'MKT-ADV', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Operational Expenses', 'code' => 'OPR-EXP', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Miscellaneous', 'code' => 'MISC', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
