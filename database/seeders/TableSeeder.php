<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Table;

class TableSeeder extends Seeder
{
    public function run()
    {
        $tables = [
            ['table_number' => '1', 'capacity' => 2, 'status' => 'available'],
            ['table_number' => '2', 'capacity' => 2, 'status' => 'available'],
            ['table_number' => '3', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => '4', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => '5', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => '6', 'capacity' => 6, 'status' => 'available'],
            ['table_number' => '7', 'capacity' => 6, 'status' => 'available'],
            ['table_number' => '8', 'capacity' => 8, 'status' => 'available'],
            ['table_number' => '9', 'capacity' => 2, 'status' => 'available'],
            ['table_number' => '10', 'capacity' => 2, 'status' => 'available'],
            ['table_number' => '11', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => '12', 'capacity' => 4, 'status' => 'available'],
            ['table_number' => '13', 'capacity' => 6, 'status' => 'available'],
            ['table_number' => '14', 'capacity' => 8, 'status' => 'available'],
            ['table_number' => '15', 'capacity' => 10, 'status' => 'available'],
        ];

        foreach ($tables as $table) {
            $createdTable = Table::firstOrCreate(['table_number' => $table['table_number']], $table);
            // Generate QR code for each table
            $createdTable->generateQRCode();
        }
    }
}
