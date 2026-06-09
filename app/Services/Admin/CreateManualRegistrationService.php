<?php

namespace App\Services\Admin;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Participant;
use App\Models\TicketLot;
use DomainException;
use Illuminate\Support\Facades\DB;
use App\Services\TicketNumberService;

class CreateManualRegistrationService
{
    public function __construct(
        private TicketNumberService $ticketNumberService
    ){}

    public function execute(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $ticketLot = $this->getCurrentTicketLot(); // mutex aqui

            $this->ensureTicketsAvailable(); // count simples já é seguro

            $participant = Participant::query()
                ->where('cpf', $data['cpf'])
                ->lockForUpdate()
                ->first();

            if ($participant) {
                $this->ensureParticipantCanRegister($participant);
            }

            $participant ??= new Participant();
            $participant->fill([
                'cpf'               => $data['cpf'],
                'full_name'         => $data['full_name'],
                'email'             => $data['email'],
                'phone'             => $data['phone'],
                'city'              => $data['city'],
                'parish'            => $data['parish'],
                'emergency_contact' => $data['emergency_contact'] ?? null,
            ]);
            $participant->save();

            $order = Order::create([
                'participant_id' => $participant->id,
                'ticket_lot_id'  => $ticketLot->id,
                'ticket_number'  => $this->ticketNumberService->generate(),
                'status'         => 'approved',
                'amount'         => $ticketLot->price,
                'approved_at'    => now(),
            ]);

            $payment = Payment::create([
                'order_id'       => $order->id,
                'payment_method' => 'manual',
                'status'         => 'approved',
                'amount'         => $ticketLot->price,
                'paid_at'        => now(),
            ]);

            return [
                'participant' => $participant,
                'order'       => $order->load(['participant', 'ticketLot']),
                'payment'     => $payment,
            ];
        });
    }

    private function ensureTicketsAvailable(): void
    {
        $sold = Order::where('status', 'approved')->count(); // sem lockForUpdate — o mutex já está no lote

        if ($sold >= 400) {
            throw new DomainException('Ingressos esgotados.');
        }
    }

    private function ensureParticipantCanRegister(
        Participant $participant
    ): void {
        $hasActiveOrder =
            Order::query()
                ->whereParticipantId($participant->id)
                ->active()
                ->exists();

        if ($hasActiveOrder) {
            throw new DomainException(
                'Participante já possui inscrição ativa.'
            );
        }
    }

    private function getCurrentTicketLot(): TicketLot
    {
        return TicketLot::current()
            ->lockForUpdate()
            ->firstOr(
                fn () => throw new DomainException(
                    'Nenhum lote disponível.'
                )
            );
    }
}