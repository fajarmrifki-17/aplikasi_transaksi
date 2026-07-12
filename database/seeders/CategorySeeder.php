<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'PO Produk',
                'code' => 'PO-PROD',
                'description' => 'Purchase Order Produk untuk keperluan inventory dan retail.',
            ],
            [
                'name' => 'Operasional Kantor',
                'code' => 'OPERASIONAL',
                'description' => 'Pengeluaran rutin bulanan operasional kantor, utilitas, listrik, air, dan internet.',
            ],
            [
                'name' => 'Marketing & Promosi',
                'code' => 'MARKETING',
                'description' => 'Kegiatan periklanan, kampanye digital, pembuatan brosur, dan pameran.',
            ],
            [
                'name' => 'Perjalanan Dinas',
                'code' => 'TRAVEL',
                'description' => 'Akomodasi, tiket perjalanan, uang saku, dan pengeluaran terkait dinas luar kota.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['code' => $category['code']], $category);
        }
    }
}
