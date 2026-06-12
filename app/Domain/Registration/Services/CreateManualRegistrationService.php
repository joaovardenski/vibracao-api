<?php

namespace App\Domain\Registration\Services;

use App\Domain\Ticket\Services\TicketNumberService;
use App\Models\Order;
use App\Models\Participant;
use App\Models\Payment;
use App\Models\TicketLot;
use Illuminate\Support\Facades\DB;

class CreateManualRegistrationService
{
    private const STATUS_APPROVED = 'approved';

    public function __construct(
        private BaseRegistrationService $baseRegistration,
        private TicketNumberService $ticketNumberService
    ) {}

    public function execute(array $data): array
    {
        return DB::transaction(function () use ($data) {
            [$participant, $ticketLot] = $this->baseRegistration->prepareRegistration($data);

            $order = Order::create([
                'participant_id' => $participant->id,
                'ticket_lot_id' => $ticketLot->id,
                'ticket_number' => $this->ticketNumberService->generate(),
                'status' => self::STATUS_APPROVED,
                'amount' => $ticketLot->price,
                'approved_at' => now(),
            ]);

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'manual',
                'status' => self::STATUS_APPROVED,
                'amount' => $ticketLot->price,
                'paid_at' => now(),
            ]);

            return [
                'participant' => $participant,
                'order' => $order->load(['participant', 'ticketLot']),
                'payment' => $payment,
            ];
        });
    }
}