<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DiningTable;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Makanan' => [
                ['Nasi Goreng Spesial', 25000],
                ['Ayam Bakar', 30000],
                ['Mie Goreng', 22000],
            ],
            'Minuman' => [
                ['Es Teh Manis', 5000],
                ['Jus Alpukat', 15000],
            ],
            'Cemilan' => [
                ['Tempe Mendoan', 12000],
                ['Kentang Goreng', 15000],
            ],
        ];

        foreach ($data as $categoryName => $menus) {
            $category = Category::updateOrCreate(['name' => $categoryName]);

            foreach ($menus as [$name, $price]) {
                Menu::updateOrCreate(
                    ['name' => $name],
                    ['category_id' => $category->id, 'price' => $price, 'is_available' => true]
                );
            }
        }

        for ($i = 1; $i <= 8; $i++) {
            DiningTable::updateOrCreate(
                ['number' => 'M-' . str_pad($i, 2, '0', STR_PAD_LEFT)],
                ['capacity' => $i <= 4 ? 2 : 4, 'status' => 'tersedia']
            );
        }
    }
}