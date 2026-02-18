<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;

class MenuItemInventorySeeder extends Seeder
{
    public function run()
    {
        // Create the pivot table if it doesn't exist
        if (!DB::getSchemaBuilder()->hasTable('menu_item_inventory')) {
            DB::statement('
                CREATE TABLE menu_item_inventory (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    menu_item_id BIGINT UNSIGNED NOT NULL,
                    inventory_item_id BIGINT UNSIGNED NOT NULL,
                    quantity_required DECIMAL(8,2) NOT NULL DEFAULT 0,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
                    FOREIGN KEY (inventory_item_id) REFERENCES inventory_items(id) ON DELETE CASCADE
                )
            ');
        }

        // Get items
        $chickenBreast = InventoryItem::where('name', 'Chicken Breast')->first();
        $beefSteak = InventoryItem::where('name', 'Beef Steak')->first();
        $salmon = InventoryItem::where('name', 'Salmon Fillet')->first();
        $shrimp = InventoryItem::where('name', 'Shrimp')->first();
        $groundBeef = InventoryItem::where('name', 'Ground Beef')->first();
        $mozzarella = InventoryItem::where('name', 'Mozzarella Cheese')->first();
        $parmesan = InventoryItem::where('name', 'Parmesan Cheese')->first();
        $pasta = InventoryItem::where('name', 'Pasta')->first();
        $tomatoes = InventoryItem::where('name', 'Tomatoes')->first();
        $lettuce = InventoryItem::where('name', 'Lettuce')->first();
        $onions = InventoryItem::where('name', 'Onions')->first();
        $flour = InventoryItem::where('name', 'Flour')->first();
        $eggs = InventoryItem::where('name', 'Eggs')->first();

        // Menu item to inventory mappings
        $mappings = [
            // Chicken Wings
            'Chicken Wings' => [
                $chickenBreast->id => 0.3,
            ],
            // Grilled Chicken Breast
            'Grilled Chicken Breast' => [
                $chickenBreast->id => 0.25,
            ],
            // Beef Steak
            'Beef Steak' => [
                $beefSteak->id => 0.3,
            ],
            // Grilled Salmon
            'Grilled Salmon' => [
                $salmon->id => 0.2,
            ],
            // Shrimp Scampi
            'Shrimp Scampi' => [
                $shrimp->id => 0.15,
                $pasta->id => 0.1,
            ],
            // Spaghetti Carbonara
            'Spaghetti Carbonara' => [
                $pasta->id => 0.1,
                $parmesan->id => 0.05,
                $eggs->id => 0.08, // 1 egg per serving
            ],
            // Fettuccine Alfredo
            'Fettuccine Alfredo' => [
                $pasta->id => 0.1,
                $parmesan->id => 0.06,
            ],
            // Margherita Pizza
            'Margherita Pizza' => [
                $flour->id => 0.15,
                $mozzarella->id => 0.1,
                $tomatoes->id => 0.05,
            ],
            // Pepperoni Pizza
            'Pepperoni Pizza' => [
                $flour->id => 0.15,
                $mozzarella->id => 0.1,
                $tomatoes->id => 0.05,
            ],
            // Caesar Salad
            'Caesar Salad' => [
                $lettuce->id => 1, // 1 head of lettuce
                $parmesan->id => 0.03,
            ],
            // Greek Salad
            'Greek Salad' => [
                $lettuce->id => 0.5,
                $tomatoes->id => 0.1,
                $onions->id => 0.05,
            ],
            // Kids Burger
            'Kids Burger' => [
                $groundBeef->id => 0.1,
                $flour->id => 0.05, // for bun
            ],
        ];

        foreach ($mappings as $menuItemName => $ingredients) {
            $menuItem = MenuItem::where('name', $menuItemName)->first();
            if ($menuItem) {
                foreach ($ingredients as $inventoryItemId => $quantity) {
                    DB::table('menu_item_inventory')->insert([
                        'menu_item_id' => $menuItem->id,
                        'inventory_item_id' => $inventoryItemId,
                        'quantity_required' => $quantity,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
