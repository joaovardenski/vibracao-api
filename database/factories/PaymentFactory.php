<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $status = fake()->randomElement([
            'pending',
            'approved',
            'rejected',
            'expired',
        ]);

        $paidAt = $status === 'approved'
            ? fake()->dateTimeBetween('-30 days', 'now')
            : null;

        return [
            'order_id' => Order::factory(),

            'gateway_payment_id' => fake()->optional()->uuid(),

            'payment_method' => fake()->randomElement([
                'pix',
                'credit_card',
            ]),

            'status' => $status,

            'amount' => fake()->randomFloat(2, 30, 50),

            'paid_at' => $paidAt,

            'raw_response' => [
                'gateway' => 'mercado_pago',
                'status_detail' => fake()->sentence(),
                'transaction_id' => fake()->uuid(),
            ],
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'paid_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'pending',
            'paid_at' => null,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => 'expired',
            'paid_at' => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => 'rejected',
            'paid_at' => null,
        ]);
    }
}
