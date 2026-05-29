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
                'Ingresso Vibração Jovem 2026 - 1º Lote',
                'Ingresso Vibração Jovem 2026 - 2º Lote',
                'Ingresso Vibração Jovem 2026 - Lote Final',
            ]),

            'price' => fake()->randomFloat(2, 25, 60),

            'starts_at' => $startDate,
            'ends_at' => $endDate,
        ];
    }
}