<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Participant;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        Participant::factory(100)
            ->create()
            ->each(function ($participant) {

                $order = Order::factory()
                    ->approved()
                    ->create([
                        'participant_id' => $participant->id,
                    ]);

                Payment::factory()
                    ->approved()
                    ->create([
                        'order_id' => $order->id,
                        'amount' => $order->amount,
                    ]);
            });

        Participant::factory(20)
            ->create()
            ->each(function ($participant) {

                $order = Order::factory()
                    ->pending()
                    ->create([
                        'participant_id' => $participant->id,
                    ]);

                Payment::factory()
                    ->pending()
                    ->create([
                        'order_id' => $order->id,
                        'amount' => $order->amount,
                    ]);
            });

        Participant::factory(10)
            ->create()
            ->each(function ($participant) {

                $order = Order::factory()
                    ->expired()
                    ->create([
                        'participant_id' => $participant->id,
                    ]);

                Payment::factory()
                    ->expired()
                    ->create([
                        'order_id' => $order->id,
                        'amount' => $order->amount,
                    ]);
            });
    }
}