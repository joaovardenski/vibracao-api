<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Participant;
use App\Models\TicketLot;
use Illuminate\Support\Facades\DB;
use DomainException;
use Exception;

class CreateRegistrationService
{
    public function __construct(private MercadoPagoService $mercadoPago) {}

    public function execute(array $data): array {
        [$participant, $order, $payment] = DB::transaction(function () use ($data) {
            $ticketLot = $this->getCurrentTicketLot();
            $this->ensureTicketsAvailable();

            $participant = Participant::query()
                ->where('cpf', $data['cpf'])
                ->lockForUpdate()
                ->first();

            if ($participant) {
                $this->ensureParticipantCanRegister($participant);
            }

            $participant = $this->createOrUpdateParticipant($participant, $data);
            $order       = $this->createOrder($participant, $ticketLot);
            $payment     = $this->createPayment($order);

            return [$participant, $order, $payment];
        });

        $preference = $this->createPreferenceOrCancel(
            $order,
            $payment
        );

        return compact('participant', 'order', 'payment', 'preference');
    }

    private function ensureTicketsAvailable(): void {
        $sold = Order::where('status', 'approved')->count();

        if ($sold >= 400) {
            throw new DomainException('Ingressos esgotados.');
        }
    }

    private function createOrUpdateParticipant(?Participant $participant, array $data): Participant {
        $participant ??= new Participant();

        $participant->fill([
            'cpf' => $data['cpf'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'city' => $data['city'],
            'parish' => $data['parish'],
            'emergency_contact' =>
                $data['emergency_contact'] ?? null,
        ]);

        $participant->save();

        return $participant;
    }

    private function ensureParticipantCanRegister(Participant $participant): void {
        $hasActiveOrder =
            Order::query()
                ->whereParticipantId(
                    $participant->id
                )
                ->active()
                ->exists();

        if ($hasActiveOrder) {
            throw new DomainException('Participante já possui inscrição ativa.');
        }
    }

    private function getCurrentTicketLot(): TicketLot {
        return TicketLot::current()
            ->lockForUpdate()
            ->firstOr(fn() => throw new DomainException('Nenhum lote disponível.'));
    }

    private function createOrder(Participant $participant, TicketLot $ticketLot): Order {
        return Order::create([
            'participant_id' => $participant->id,
            'ticket_lot_id' => $ticketLot->id,
            'status' => 'pending',
            'amount' => $ticketLot->price,
            'expires_at' => now()->addMinutes(15),
        ]);
    }

    private function createPayment(Order $order): Payment {
        return Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'pix',
            'status' => 'pending',
            'amount' => $order->amount,
        ]);
    }

    private function createPreferenceOrCancel(Order $order, Payment $payment) {
        try {
            $preference =
                $this->mercadoPago
                    ->createPreference(
                        title: $order->ticketLot->name,
                        price: (float) $payment->amount,
                        externalReference: (string) $order->id,
                        participant: $order->participant,
                    );

            $payment->update([
                'gateway_payment_id' => $preference->id
            ]);

            return $preference;

        } catch (Exception $e) {
            $order->update([
                'status' => 'cancelled'
            ]);

            $payment->update([
                'status' => 'cancelled'
            ]);

            throw $e;
        }
    }
}