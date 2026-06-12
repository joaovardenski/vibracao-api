<?php

namespace App\Services\Admin;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListRegistrationsService
{
    public function execute(
        int $perPage = 15,
        ?string $search = null,
    ): LengthAwarePaginator {

        return Order::query()
            ->with([
                'participant',
                'ticketLot'
            ])

            ->where('status', 'approved')

            ->when(
                $search,
                function ($query) use ($search) {
                    $query->whereHas(
                        'participant',
                        function ($participantQuery) use ($search) {
                            $participantQuery
                                ->where('full_name', 'ilike', "%{$search}%")
                                ->orWhere('cpf', 'ilike', "%{$search}%")
                                ->orWhere('email', 'ilike', "%{$search}%");
                        }
                    );
                }
            )

            ->latest()

            ->paginate($perPage);
    }
}