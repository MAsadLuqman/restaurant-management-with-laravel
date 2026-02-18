<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryItem;

class InventoryItemSeeder extends Seeder
{
    public function run()
    {
        $inventoryItems = [
            // Proteins
            [
                'name' => 'Chicken Breast',
                'description' => 'Fresh chicken breast',
                'unit' => 'kg',
                'current_stock' => 50.00,
                'minimum_stock' => 10.00,
                'unit_cost' => 8.50,
                'is_active' => true,
            ],
            [
                'name' => 'Beef Steak',
                'description' => 'Premium beef steak',
                'unit' => 'kg',
                'current_stock' => 30.00,
                'minimum_stock' => 5.00,
                'unit_cost' => 25.00,
                'is_active' => true,
            ],
            [
                'name' => 'Salmon Fillet',
                'description' => 'Fresh salmon fillet',
                'unit' => 'kg',
                'current_stock' => 20.00,
                'minimum_stock' => 3.00,
                'unit_cost' => 18.00,
                'is_active' => true,
            ],
            [
                'name' => 'Shrimp',
                'description' => 'Large shrimp',
                'unit' => 'kg',
                'current_stock' => 15.00,
                'minimum_stock' => 2.00,
                'unit_cost' => 22.00,
                'is_active' => true,
            ],
            [
                'name' => 'Ground Beef',
                'description' => 'Ground beef for burgers',
                'unit' => 'kg',
                'current_stock' => 25.00,
                'minimum_stock' => 5.00,
                'unit_cost' => 12.00,
                'is_active' => true,
            ],

            // Vegetables
            [
                'name' => 'Tomatoes',
                'description' => 'Fresh tomatoes',
                'unit' => 'kg',
                'current_stock' => 40.00,
                'minimum_stock' => 8.00,
                'unit_cost' => 3.50,
                'is_active' => true,
            ],
            [
                'name' => 'Onions',
                'description' => 'Yellow onions',
                'unit' => 'kg',
                'current_stock' => 35.00,
                'minimum_stock' => 10.00,
                'unit_cost' => 2.00,
                'is_active' => true,
            ],
            [
                'name' => 'Bell Peppers',
                'description' => 'Mixed bell peppers',
                'unit' => 'kg',
                'current_stock' => 20.00,
                'minimum_stock' => 5.00,
                'unit_cost' => 4.00,
                'is_active' => true,
            ],
            [
                'name' => 'Lettuce',
                'description' => 'Fresh lettuce',
                'unit' => 'pieces',
                'current_stock' => 50.00,
                'minimum_stock' => 10.00,
                'unit_cost' => 1.50,
                'is_active' => true,
            ],
            [
                'name' => 'Mushrooms',
                'description' => 'Button mushrooms',
                'unit' => 'kg',
                'current_stock' => 15.00,
                'minimum_stock' => 3.00,
                'unit_cost' => 6.00,
                'is_active' => true,
            ],

            // Dairy
            [
                'name' => 'Mozzarella Cheese',
                'description' => 'Fresh mozzarella cheese',
                'unit' => 'kg',
                'current_stock' => 25.00,
                'minimum_stock' => 5.00,
                'unit_cost' => 12.00,
                'is_active' => true,
            ],
            [
                'name' => 'Parmesan Cheese',
                'description' => 'Grated parmesan cheese',
                'unit' => 'kg',
                'current_stock' => 10.00,
                'minimum_stock' => 2.00,
                'unit_cost' => 18.00,
                'is_active' => true,
            ],
            [
                'name' => 'Heavy Cream',
                'description' => 'Heavy cooking cream',
                'unit' => 'liters',
                'current_stock' => 20.00,
                'minimum_stock' => 5.00,
                'unit_cost' => 4.50,
                'is_active' => true,
            ],
            [
                'name' => 'Butter',
                'description' => 'Unsalted butter',
                'unit' => 'kg',
                'current_stock' => 15.00,
                'minimum_stock' => 3.00,
                'unit_cost' => 8.00,
                'is_active' => true,
            ],
            [
                'name' => 'Eggs',
                'description' => 'Fresh eggs',
                'unit' => 'dozen',
                'current_stock' => 100.00,
                'minimum_stock' => 20.00,
                'unit_cost' => 3.00,
                'is_active' => true,
            ],

            // Pantry Items
            [
                'name' => 'Pasta',
                'description' => 'Various pasta types',
                'unit' => 'kg',
                'current_stock' => 50.00,
                'minimum_stock' => 10.00,
                'unit_cost' => 2.50,
                'is_active' => true,
            ],
            [
                'name' => 'Rice',
                'description' => 'Long grain rice',
                'unit' => 'kg',
                'current_stock' => 40.00,
                'minimum_stock' => 10.00,
                'unit_cost' => 2.00,
                'is_active' => true,
            ],
            [
                'name' => 'Flour',
                'description' => 'All-purpose flour',
                'unit' => 'kg',
                'current_stock' => 30.00,
                'minimum_stock' => 8.00,
                'unit_cost' => 1.50,
                'is_active' => true,
            ],
            [
                'name' => 'Olive Oil',
                'description' => 'Extra virgin olive oil',
                'unit' => 'liters',
                'current_stock' => 25.00,
                'minimum_stock' => 5.00,
                'unit_cost' => 12.00,
                'is_active' => true,
            ],
            [
                'name' => 'Salt',
                'description' => 'Table salt',
                'unit' => 'kg',
                'current_stock' => 20.00,
                'minimum_stock' => 5.00,
                'unit_cost' => 1.00,
                'is_active' => true,
            ],

            // Beverages
            [
                'name' => 'Coffee Beans',
                'description' => 'Premium coffee beans',
                'unit' => 'kg',
                'current_stock' => 10.00,
                'minimum_stock' => 2.00,
                'unit_cost' => 15.00,
                'is_active' => true,
            ],
            [
                'name' => 'Tea Bags',
                'description' => 'Assorted tea bags',
                'unit' => 'boxes',
                'current_stock' => 20.00,
                'minimum_stock' => 5.00,
                'unit_cost' => 8.00,
                'is_active' => true,
            ],
            [
                'name' => 'Soft Drink Syrup',
                'description' => 'Cola syrup concentrate',
                'unit' => 'liters',
                'current_stock' => 15.00,
                'minimum_stock' => 3.00,
                'unit_cost' => 25.00,
                'is_active' => true,
            ],
            [
                'name' => 'Orange Juice',
                'description' => 'Fresh orange juice',
                'unit' => 'liters',
                'current_stock' => 30.00,
                'minimum_stock' => 8.00,
                'unit_cost' => 6.00,
                'is_active' => true,
            ],

            // Frozen Items
            [
                'name' => 'French Fries',
                'description' => 'Frozen french fries',
                'unit' => 'kg',
                'current_stock' => 40.00,
                'minimum_stock' => 10.00,
                'unit_cost' => 3.00,
                'is_active' => true,
            ],
            [
                'name' => 'Ice Cream',
                'description' => 'Vanilla ice cream',
                'unit' => 'liters',
                'current_stock' => 20.00,
                'minimum_stock' => 5.00,
                'unit_cost' => 8.00,
                'is_active' => true,
            ]
        ];

        foreach ($inventoryItems as $item) {
            InventoryItem::firstOrCreate(['name' => $item['name']], $item);
        }
    }
}
