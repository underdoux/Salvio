<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Obat Bebas', 'description' => 'Obat yang dapat dibeli tanpa resep dokter'],
            ['name' => 'Obat Bebas Terbatas', 'description' => 'Obat yang dapat dibeli tanpa resep dokter dalam jumlah terbatas'],
            ['name' => 'Obat Keras', 'description' => 'Obat yang hanya dapat dibeli dengan resep dokter'],
            ['name' => 'Narkotika', 'description' => 'Obat yang penggunaannya diawasi ketat'],
            ['name' => 'Obat Herbal', 'description' => 'Obat yang berasal dari bahan alami'],
            ['name' => 'Suplemen', 'description' => 'Produk tambahan nutrisi'],
            ['name' => 'Alat Kesehatan', 'description' => 'Peralatan medis dan kesehatan'],
            ['name' => 'Kosmetik', 'description' => 'Produk perawatan dan kecantikan'],
            ['name' => 'Makanan & Minuman', 'description' => 'Produk konsumsi dengan izin BPOM'],
            ['name' => 'Lainnya', 'description' => 'Kategori lainnya']
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}
