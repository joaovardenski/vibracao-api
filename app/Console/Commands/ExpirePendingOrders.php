<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpirePendingOrders extends Command
{
    protected $signature =
        'orders:expire';

    protected $description =
        'Expira reservas pendentes vencidas';

    public function handle(): int
    {
        DB::transaction(function () {

            $orders =
                Order::query()
                    ->where('status', 'pending')
                    ->where(
                        'expires_at',
                        '<=',
                        now()
                    )
                    ->with('payment')
                    ->get();

            foreach ($orders as $order) {

                $order->update([
                    'status' => 'expired',
                    'expires_at' => null,
                ]);

                $order
                    ->payment()
                    ->update([
                        'status' => 'expired',
                    ]);
            }
        });

        $this->info(
            'Reservas expiradas com sucesso.'
        );

        return self::SUCCESS;
    }
}
