<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegistrationRequest;
use App\Services\CreateRegistrationService;
use App\Models\Order;

class RegistrationController extends Controller
{
    public function store(RegistrationRequest $request, CreateRegistrationService $service) {
        $result = $service->execute($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Inscrição iniciada.',
            'data' => $result,
        ], 201);
    }

    public function status(Order $order)
    {
        $order->load([
            'participant',
            'ticketLot',
            'payment',
        ]);

        return response()->json([
            'status' => $order->payment?->status ?? 'pending',

            'participant' => [
                'name' => $order->participant->full_name,
                'email' => $order->participant->email,
            ],

            'ticket' => [
                'lot' => $order->ticketLot->name,
                'amount' => $order->amount,
                'ticket_number' => $order->ticket_number,
            ],
        ]);
    }
}
