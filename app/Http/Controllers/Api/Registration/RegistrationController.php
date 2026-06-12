<?php

namespace App\Http\Controllers\Api\Registration;

use App\Domain\Registration\Services\CreateRegistrationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class RegistrationController extends Controller
{
    public function __construct(
        private readonly CreateRegistrationService $createRegistrationService
    ) {}

    public function store(RegistrationRequest $request): JsonResponse
    {
        $result = $this->createRegistrationService->execute($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Inscrição iniciada.',
            'data' => $result,
        ], 201);
    }

    public function status(Order $order): JsonResponse
    {
        $order->loadMissing(['participant', 'ticketLot', 'payment']);

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