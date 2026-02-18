<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;
use App\Models\Category;

class MenuItemSeeder extends Seeder
{
    public function run()
    {
        $appetizers = Category::where('name', 'Appetizers')->first();
        $soups = Category::where('name', 'Soups')->first();
        $salads = Category::where('name', 'Salads')->first();
        $mainCourse = Category::where('name', 'Main Course')->first();
        $pasta = Category::where('name', 'Pasta')->first();
        $pizza = Category::where('name', 'Pizza')->first();
        $seafood = Category::where('name', 'Seafood')->first();
        $desserts = Category::where('name', 'Desserts')->first();
        $beverages = Category::where('name', 'Beverages')->first();
        $kidsMenu = Category::where('name', 'Kids Menu')->first();

        $menuItems = [
            // Appetizers
            [
                'name' => 'Chicken Wings',
                'description' => 'Crispy chicken wings with buffalo sauce',
                'price' => 12.99,
                'category_id' => $appetizers->id,
                'is_available' => true,
                'preparation_time' => 15,
            ],
            [
                'name' => 'Mozzarella Sticks',
                'description' => 'Golden fried mozzarella with marinara sauce',
                'price' => 9.99,
                'category_id' => $appetizers->id,
                'is_available' => true,
                'preparation_time' => 10,
            ],
            [
                'name' => 'Nachos Supreme',
                'description' => 'Loaded nachos with cheese, jalapeños, and sour cream',
                'price' => 14.99,
                'category_id' => $appetizers->id,
                'is_available' => true,
                'preparation_time' => 12,
            ],
            [
                'name' => 'Garlic Bread',
                'description' => 'Toasted bread with garlic butter and herbs',
                'price' => 6.99,
                'category_id' => $appetizers->id,
                'is_available' => true,
                'preparation_time' => 8,
            ],

            // Soups
            [
                'name' => 'Tomato Basil Soup',
                'description' => 'Creamy tomato soup with fresh basil',
                'price' => 7.99,
                'category_id' => $soups->id,
                'is_available' => true,
                'preparation_time' => 10,
            ],
            [
                'name' => 'Chicken Noodle Soup',
                'description' => 'Classic chicken soup with vegetables and noodles',
                'price' => 8.99,
                'category_id' => $soups->id,
                'is_available' => true,
                'preparation_time' => 12,
            ],
            [
                'name' => 'Mushroom Soup',
                'description' => 'Creamy mushroom soup with herbs',
                'price' => 8.49,
                'category_id' => $soups->id,
                'is_available' => true,
                'preparation_time' => 10,
            ],

            // Salads
            [
                'name' => 'Caesar Salad',
                'description' => 'Romaine lettuce with Caesar dressing and croutons',
                'price' => 11.99,
                'category_id' => $salads->id,
                'is_available' => true,
                'preparation_time' => 8,
            ],
            [
                'name' => 'Greek Salad',
                'description' => 'Mixed greens with feta cheese, olives, and tomatoes',
                'price' => 12.99,
                'category_id' => $salads->id,
                'is_available' => true,
                'preparation_time' => 10,
            ],
            [
                'name' => 'Garden Salad',
                'description' => 'Fresh mixed greens with seasonal vegetables',
                'price' => 9.99,
                'category_id' => $salads->id,
                'is_available' => true,
                'preparation_time' => 7,
            ],

            // Main Course
            [
                'name' => 'Grilled Chicken Breast',
                'description' => 'Seasoned grilled chicken with vegetables and rice',
                'price' => 18.99,
                'category_id' => $mainCourse->id,
                'is_available' => true,
                'preparation_time' => 25,
            ],
            [
                'name' => 'Beef Steak',
                'description' => 'Premium beef steak with mashed potatoes',
                'price' => 28.99,
                'category_id' => $mainCourse->id,
                'is_available' => true,
                'preparation_time' => 30,
            ],
            [
                'name' => 'Pork Ribs',
                'description' => 'BBQ pork ribs with coleslaw and fries',
                'price' => 22.99,
                'category_id' => $mainCourse->id,
                'is_available' => true,
                'preparation_time' => 35,
            ],
            [
                'name' => 'Lamb Chops',
                'description' => 'Grilled lamb chops with mint sauce',
                'price' => 26.99,
                'category_id' => $mainCourse->id,
                'is_available' => true,
                'preparation_time' => 28,
            ],

            // Pasta
            [
                'name' => 'Spaghetti Carbonara',
                'description' => 'Classic carbonara with bacon and parmesan',
                'price' => 16.99,
                'category_id' => $pasta->id,
                'is_available' => true,
                'preparation_time' => 18,
            ],
            [
                'name' => 'Fettuccine Alfredo',
                'description' => 'Creamy alfredo sauce with fettuccine pasta',
                'price' => 15.99,
                'category_id' => $pasta->id,
                'is_available' => true,
                'preparation_time' => 15,
            ],
            [
                'name' => 'Penne Arrabbiata',
                'description' => 'Spicy tomato sauce with penne pasta',
                'price' => 14.99,
                'category_id' => $pasta->id,
                'is_available' => true,
                'preparation_time' => 16,
            ],
            [
                'name' => 'Lasagna',
                'description' => 'Layered pasta with meat sauce and cheese',
                'price' => 19.99,
                'category_id' => $pasta->id,
                'is_available' => true,
                'preparation_time' => 25,
            ],

            // Pizza
            [
                'name' => 'Margherita Pizza',
                'description' => 'Classic pizza with tomato, mozzarella, and basil',
                'price' => 16.99,
                'category_id' => $pizza->id,
                'is_available' => true,
                'preparation_time' => 20,
            ],
            [
                'name' => 'Pepperoni Pizza',
                'description' => 'Pizza with pepperoni and mozzarella cheese',
                'price' => 18.99,
                'category_id' => $pizza->id,
                'is_available' => true,
                'preparation_time' => 20,
            ],
            [
                'name' => 'Supreme Pizza',
                'description' => 'Loaded pizza with multiple toppings',
                'price' => 22.99,
                'category_id' => $pizza->id,
                'is_available' => true,
                'preparation_time' => 25,
            ],
            [
                'name' => 'Hawaiian Pizza',
                'description' => 'Pizza with ham and pineapple',
                'price' => 19.99,
                'category_id' => $pizza->id,
                'is_available' => true,
                'preparation_time' => 22,
            ],

            // Seafood
            [
                'name' => 'Grilled Salmon',
                'description' => 'Fresh salmon with lemon and herbs',
                'price' => 24.99,
                'category_id' => $seafood->id,
                'is_available' => true,
                'preparation_time' => 22,
            ],
            [
                'name' => 'Fish and Chips',
                'description' => 'Battered fish with crispy fries',
                'price' => 17.99,
                'category_id' => $seafood->id,
                'is_available' => true,
                'preparation_time' => 18,
            ],
            [
                'name' => 'Shrimp Scampi',
                'description' => 'Garlic butter shrimp with pasta',
                'price' => 21.99,
                'category_id' => $seafood->id,
                'is_available' => true,
                'preparation_time' => 20,
            ],
            [
                'name' => 'Lobster Tail',
                'description' => 'Grilled lobster tail with butter sauce',
                'price' => 32.99,
                'category_id' => $seafood->id,
                'is_available' => true,
                'preparation_time' => 25,
            ],

            // Desserts
            [
                'name' => 'Chocolate Cake',
                'description' => 'Rich chocolate cake with chocolate frosting',
                'price' => 7.99,
                'category_id' => $desserts->id,
                'is_available' => true,
                'preparation_time' => 5,
            ],
            [
                'name' => 'Cheesecake',
                'description' => 'New York style cheesecake with berry sauce',
                'price' => 8.99,
                'category_id' => $desserts->id,
                'is_available' => true,
                'preparation_time' => 5,
            ],
            [
                'name' => 'Tiramisu',
                'description' => 'Italian coffee-flavored dessert',
                'price' => 9.99,
                'category_id' => $desserts->id,
                'is_available' => true,
                'preparation_time' => 5,
            ],
            [
                'name' => 'Ice Cream Sundae',
                'description' => 'Vanilla ice cream with toppings',
                'price' => 6.99,
                'category_id' => $desserts->id,
                'is_available' => true,
                'preparation_time' => 3,
            ],

            // Beverages
            [
                'name' => 'Coffee',
                'description' => 'Freshly brewed coffee',
                'price' => 3.99,
                'category_id' => $beverages->id,
                'is_available' => true,
                'preparation_time' => 3,
            ],
            [
                'name' => 'Tea',
                'description' => 'Selection of hot teas',
                'price' => 3.49,
                'category_id' => $beverages->id,
                'is_available' => true,
                'preparation_time' => 3,
            ],
            [
                'name' => 'Soft Drinks',
                'description' => 'Coca-Cola, Pepsi, Sprite, etc.',
                'price' => 2.99,
                'category_id' => $beverages->id,
                'is_available' => true,
                'preparation_time' => 1,
            ],
            [
                'name' => 'Fresh Orange Juice',
                'description' => 'Freshly squeezed orange juice',
                'price' => 4.99,
                'category_id' => $beverages->id,
                'is_available' => true,
                'preparation_time' => 2,
            ],
            [
                'name' => 'Milkshake',
                'description' => 'Vanilla, chocolate, or strawberry',
                'price' => 5.99,
                'category_id' => $beverages->id,
                'is_available' => true,
                'preparation_time' => 5,
            ],

            // Kids Menu
            [
                'name' => 'Kids Chicken Nuggets',
                'description' => 'Crispy chicken nuggets with fries',
                'price' => 8.99,
                'category_id' => $kidsMenu->id,
                'is_available' => true,
                'preparation_time' => 12,
            ],
            [
                'name' => 'Kids Mac and Cheese',
                'description' => 'Creamy macaroni and cheese',
                'price' => 7.99,
                'category_id' => $kidsMenu->id,
                'is_available' => true,
                'preparation_time' => 10,
            ],
            [
                'name' => 'Kids Pizza',
                'description' => 'Small cheese pizza',
                'price' => 9.99,
                'category_id' => $kidsMenu->id,
                'is_available' => true,
                'preparation_time' => 15,
            ],
            [
                'name' => 'Kids Burger',
                'description' => 'Small burger with fries',
                'price' => 10.99,
                'category_id' => $kidsMenu->id,
                'is_available' => true,
                'preparation_time' => 15,
            ]
        ];

        foreach ($menuItems as $item) {
            MenuItem::firstOrCreate(['name' => $item['name']], $item);
        }
    }
}
