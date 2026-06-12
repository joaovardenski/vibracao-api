<?php

namespace App\Domain\Registration\Services;

use App\Models\Participant;
use App\Models\TicketLot;
use App\Models\Order;
use DomainException;

class BaseRegistrationService
{
    private const MAX_TICKETS_SOLD = 400;

    public function prepareRegistration(array $data): array
    {
        $ticketLot = $this->getCurrentTicketLot();
        $this->ensureTicketsAreAvailable();

        $participant = Participant::query()
            ->where('cpf', $data['cpf'])
            ->lockForUpdate()
            ->first();

        if ($participant) {
            $this->ensureParticipantCanRegister($participant);
        }

        $participant = $this->createOrUpdateParticipant($participant, $data);

        return [$participant, $ticketLot];
    }

    private function ensureTicketsAreAvailable(): void
    {
        $soldCount = Order::where('status', 'approved')->count();

        if ($soldCount >= self::MAX_TICKETS_SOLD) {
            throw new DomainException('Ingressos esgotados.');
        }
    }

    private function ensureParticipantCanRegister(Participant $participant): void
    {
        $hasActiveOrder = Order::query()
            ->whereParticipantId($participant->id)
            ->active()
            ->exists();

        if ($hasActiveOrder) {
            throw new DomainException('Participante já possui inscrição ativa.');
        }
    }

    private function createOrUpdateParticipant(?Participant $participant, array $data): Participant
    {
        $participant ??= new Participant();

        $participant->fill([
            'cpf' => $data['cpf'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'city' => $data['city'],
            'parish' => $data['parish'],
            'emergency_contact' => $data['emergency_contact'] ?? null,
        ]);

        $participant->save();

        return $participant;
    }

    private function getCurrentTicketLot(): TicketLot
    {
        return TicketLot::current()
            ->lockForUpdate()
            ->firstOr(fn () => throw new DomainException('Nenhum lote disponível.'));
    }
}