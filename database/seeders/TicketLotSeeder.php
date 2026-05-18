<?php

namespace Database\Seeders;

use App\Models\TicketLot;
use Illuminate\Database\Seeder;

class TicketLotSeeder extends Seeder
{
    public function run(): void
    {
        TicketLot::create([
            'name' => '1º Lote',
            'price' => 30.00,
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->addDays(10),
            'is_active' => true,
        ]);

        TicketLot::create([
            'name' => '2º Lote',
            'price' => 35.00,
            'starts_at' => now()->addDays(11),
            'ends_at' => now()->addDays(30),
            'is_active' => false,
        ]);
    }
}