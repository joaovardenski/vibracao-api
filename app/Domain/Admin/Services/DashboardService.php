<?php

namespace App\Domain\Admin\Services;

use App\Models\Order;

class DashboardService
{
    public function getStats(): array
    {
        $approvedCount = Order::where('status', 'approved')->count();

        return [
            'approved' => $approvedCount,
            'pending' => Order::where('status', 'pending')->count(),
            'expired' => Order::where('status', 'expired')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('status', 'approved')->sum('amount'),
            'remaining_spots' => max(0, 400 - $approvedCount),
            'latest_registrations' => Order::query()
                ->with([
                    'participant:id,full_name,city',
                    'ticketLot:id,name',
                ])
                ->latest()
                ->limit(10)
                ->get([
                    'id',
                    'participant_id',
                    'ticket_lot_id',
                    'ticket_number',
                    'status',
                    'amount',
                ]),
        ];
    }
}