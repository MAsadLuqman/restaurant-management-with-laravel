<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;

class SampleReservationSeeder extends Seeder
{
    public function run()
    {
        $tables = Table::all();
        $statuses = ['pending', 'confirmed', 'seated', 'completed'];

        // Create reservations for the next 30 days
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->addDays($i);
            $reservationsPerDay = rand(3, 8);

            for ($j = 0; $j < $reservationsPerDay; $j++) {
                $table = $tables->random();
                $reservationTime = $date->copy()->addHours(rand(11, 21))->addMinutes([0, 15, 30, 45][rand(0, 3)]);

                Reservation::create([
                    'customer_name' => 'Customer ' . rand(1, 200),
                    'customer_phone' => '+1234567' . rand(100, 999),
                    'customer_email' => 'customer' . rand(1, 200) . '@email.com',
                    'table_id' => $table->id,
                    'reservation_date' => $reservationTime,
                    'party_size' => rand(2, min(8, $table->capacity)),
                    'status' => $i < 2 ? $statuses[rand(0, 1)] : 'confirmed', // Recent reservations might be pending
                    'special_requests' => rand(0, 1) ? 'Window seat preferred' : null,
                ]);
            }
        }
    }
}
