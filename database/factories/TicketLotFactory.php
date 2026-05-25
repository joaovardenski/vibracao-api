<?php

namespace Database\Factories;

use App\Models\TicketLot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketLot>
 */
class TicketLotFactory extends Factory
{
    protected $model = TicketLot::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
        $endDate = (clone $startDate)->modify('+15 days');

        return [
            'name' => fake()->randomElement([
                '1º Lote',
                '2º Lote',
                'Lote Promocional',
                'Lote Final',
            ]),

            'price' => fake()->randomFloat(2, 25, 60),

            'starts_at' => $startDate,
            'ends_at' => $endDate,
        ];
    }
}