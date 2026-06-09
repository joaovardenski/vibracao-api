<?php

namespace App\Services\Admin;

use App\Models\Order;

class DashboardService
{
    public function getStats(): array
    {
        return [
            'approved' => Order::where('status', 'approved')->count(),
            'pending' => Order::where('status', 'pending')->count(),
            'expired' => Order::where('status', 'expired')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),

            'total_revenue' => Order::where('status', 'approved')->sum('amount'),

            'remaining_spots' => 400 - Order::where('status', 'approved')->count(),

            'latest_registrations' => Order::query()
                ->with([
                    'participant:id,full_name,city',
                    'ticketLot:id,name'
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
