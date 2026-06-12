<?php

namespace App\Domain\MercadoPago\Services;

use App\Domain\Ticket\Services\TicketNumberService;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class HandlePaymentWebhookService
{
    private const WEBHOOK_TYPE_PAYMENT = 'payment';
    private const STATUS_APPROVED = 'approved';
    private const STATUS_CANCELLED = 'cancelled';

    public function __construct(
        private MercadoPagoService $mercadoPago,
        private TicketNumberService $ticketNumberService
    ) {}

    public function execute(array $payload): void
    {
        if (($payload['type'] ?? null) !== self::WEBHOOK_TYPE_PAYMENT) {
            return;
        }

        $gatewayPaymentId = $payload['data']['id'];
        $gatewayPayment = $this->mercadoPago->getPayment($gatewayPaymentId);

        $payment = $this->findPayment($gatewayPayment);

        if (! $payment) {
            return;
        }

        $this->syncStatus($payment, $gatewayPayment);
    }

    private function findPayment(object $gatewayPayment): ?Payment
    {
        /** @var Payment|null $payment */
        $payment = Payment::query()
            ->where('gateway_payment_id', $gatewayPayment->id)
            ->first();

        if (! $payment) {
            $payment = Payment::query()
                ->whereHas('order', fn ($query) => $query->where('id', $gatewayPayment->external_reference))
                ->first();
        }

        return $payment;
    }

    private function syncStatus(Payment $payment, object $gatewayPayment): void
    {
        if ($payment->status === self::STATUS_APPROVED) {
            return;
        }

        DB::transaction(function () use ($payment, $gatewayPayment) {
            $payment->update([
                'status' => $gatewayPayment->status,
                'paid_at' => $gatewayPayment->status === self::STATUS_APPROVED ? now() : null,
                'raw_response' => $this->buildRawResponse($gatewayPayment),
            ]);

            $this->updateOrderStatus($payment->order, $gatewayPayment->status);
        });
    }

    private function updateOrderStatus($order, string $gatewayStatus): void
    {
        match ($gatewayStatus) {
            self::STATUS_APPROVED => $order->update([
                'status' => self::STATUS_APPROVED,
                'approved_at' => now(),
                'ticket_number' => $this->ticketNumberService->generate(),
                'expires_at' => null,
            ]),
            'rejected', self::STATUS_CANCELLED => $order->update([
                'status' => self::STATUS_CANCELLED,
            ]),
            default => null,
        };
    }

    private function buildRawResponse(object $gateway): array
    {
        return [
            'gateway_id' => $gateway->id,
            'status' => $gateway->status,
            'status_detail' => $gateway->status_detail,
            'payment_method' => $gateway->payment_method_id,
            'transaction_amount' => $gateway->transaction_amount,
            'external_reference' => $gateway->external_reference,
            'approved_at' => $gateway->date_approved,
            'created_at' => $gateway->date_created,
            'collector_id' => $gateway->collector_id,
            'payer' => [
                'id' => $gateway->payer?->id,
            ],
            'pix' => [
                'transaction_id' => $gateway->transaction_details?->transaction_id,
                'bank_transfer_id' => $gateway->transaction_details?->bank_transfer_id,
            ],
        ];
    }
}