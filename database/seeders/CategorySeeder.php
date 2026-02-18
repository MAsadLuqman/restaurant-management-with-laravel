<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Appetizers',
                'description' => 'Start your meal with our delicious appetizers',
                'is_active' => true,
            ],
            [
                'name' => 'Soups',
                'description' => 'Warm and comforting soups',
                'is_active' => true,
            ],
            [
                'name' => 'Salads',
                'description' => 'Fresh and healthy salads',
                'is_active' => true,
            ],
            [
                'name' => 'Main Course',
                'description' => 'Our signature main course dishes',
                'is_active' => true,
            ],
            [
                'name' => 'Pasta',
                'description' => 'Italian pasta dishes',
                'is_active' => true,
            ],
            [
                'name' => 'Pizza',
                'description' => 'Wood-fired pizzas',
                'is_active' => true,
            ],
            [
                'name' => 'Seafood',
                'description' => 'Fresh seafood specialties',
                'is_active' => true,
            ],
            [
                'name' => 'Desserts',
                'description' => 'Sweet endings to your meal',
                'is_active' => true,
            ],
            [
                'name' => 'Beverages',
                'description' => 'Hot and cold beverages',
                'is_active' => true,
            ],
            [
                'name' => 'Kids Menu',
                'description' => 'Special dishes for children',
                'is_active' => true,
            ]
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
