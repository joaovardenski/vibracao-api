<?php

namespace App\Domain\Registration\Services;

use App\Domain\MercadoPago\Services\MercadoPagoService;
use App\Models\Order;
use App\Models\Participant;
use App\Models\Payment;
use App\Models\TicketLot;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateRegistrationService
{
    private const EXPIRATION_MINUTES = 15;
    private const STATUS_PENDING = 'pending';
    private const STATUS_CANCELLED = 'cancelled';

    public function __construct(
        private BaseRegistrationService $baseRegistration,
        private MercadoPagoService $mercadoPago
    ) {}

    public function execute(array $data): array
    {
        [$participant, $order, $payment] = DB::transaction(function () use ($data) {
            [$participant, $ticketLot] = $this->baseRegistration->prepareRegistration($data);

            $order = $this->createOrder($participant, $ticketLot);
            $payment = $this->createPayment($order);

            return [$participant, $order, $payment];
        });

        $preference = $this->createPreferenceOrCancel($order, $payment);

        return compact('participant', 'order', 'payment', 'preference');
    }

    private function createOrder(Participant $participant, TicketLot $ticketLot): Order
    {
        return Order::create([
            'participant_id' => $participant->id,
            'ticket_lot_id' => $ticketLot->id,
            'status' => self::STATUS_PENDING,
            'amount' => $ticketLot->price,
            'expires_at' => now()->addMinutes(self::EXPIRATION_MINUTES),
        ]);
    }

    private function createPayment(Order $order): Payment
    {
        return Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'pix',
            'status' => self::STATUS_PENDING,
            'amount' => $order->amount,
        ]);
    }

    private function createPreferenceOrCancel(Order $order, Payment $payment): object
    {
        try {
            $preference = $this->mercadoPago->createPreference(
                title: $order->ticketLot->name,
                price: (float) $payment->amount,
                externalReference: (string) $order->id,
                participant: $order->participant,
            );

            $payment->update(['gateway_payment_id' => $preference->id]);

            return $preference;
        } catch (Exception $e) {
            $order->update(['status' => self::STATUS_CANCELLED]);
            $payment->update(['status' => self::STATUS_CANCELLED]);

            throw $e;
        }
    }
}