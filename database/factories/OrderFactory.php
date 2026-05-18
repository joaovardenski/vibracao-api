<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Participant;
use App\Models\TicketLot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $status = fake()->randomElement([
            'pending',
            'approved',
            'expired',
        ]);

        $approvedAt = $status === 'approved'
            ? fake()->dateTimeBetween('-30 days', 'now')
            : null;

        $expiresAt = $status === 'pending'
            ? fake()->dateTimeBetween('now', '+15 minutes')
            : fake()->dateTimeBetween('-30 days', '-1 day');

        return [
            'participant_id' => Participant::factory(),

            'ticket_lot_id' => TicketLot::factory(),

            'ticket_number' => 'VJ2026-' . fake()->unique()->numerify('####'),

            'status' => $status,

            'amount' => fake()->randomFloat(2, 30, 50),

            'expires_at' => $expiresAt,

            'approved_at' => $approvedAt,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'approved_at' => now(),
            'expires_at' => now()->subMinutes(10),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'pending',
            'approved_at' => null,
            'expires_at' => now()->addMinutes(15),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => 'expired',
            'approved_at' => null,
            'expires_at' => now()->subMinutes(15),
        ]);
    }
}