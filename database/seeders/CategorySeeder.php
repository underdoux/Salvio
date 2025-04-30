<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Obat Keras',
                'description' => 'Obat yang hanya dapat dibeli dengan resep dokter'
            ],
            [
                'name' => 'Obat Bebas Terbatas',
                'description' => 'Obat yang dapat dibeli tanpa resep dokter namun dengan batasan jumlah dan penggunaan'
            ],
            [
                'name' => 'Obat Bebas',
                'description' => 'Obat yang dapat dibeli secara bebas'
            ],
            [
                'name' => 'Alat Kesehatan',
                'description' => 'Peralatan medis dan kesehatan'
            ],
            [
                'name' => 'Suplemen',
                'description' => 'Vitamin dan suplemen kesehatan'
            ]
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}
