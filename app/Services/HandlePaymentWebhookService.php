<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class HandlePaymentWebhookService
{
    public function __construct(
        private MercadoPagoService $mercadoPago
    ) {}

    public function execute(array $payload): void
    {
        if (($payload['type'] ?? null) !== 'payment') {
            return;
        }

        $gatewayPaymentId = $payload['data']['id'];
        $gatewayPayment   = $this->mercadoPago->getPayment($gatewayPaymentId);

        // Busca pelo ID do pagamento no gateway (correto)
        $payment = Payment::query()
            ->where('gateway_payment_id', $gatewayPayment->id)
            ->first();

        // Fallback: busca pelo external_reference (order_id) caso
        // o gateway_payment_id ainda não tenha sido salvo
        if (!$payment) {
            $payment = Payment::query()
                ->whereHas('order', fn($q) =>
                    $q->where('id', $gatewayPayment->external_reference)
                )
                ->first();
        }

        if (!$payment) {
            return;
        }

        $this->syncStatus($payment, $gatewayPayment);
    }

    private function syncStatus(Payment $payment, object $gateway): void
    {
        DB::transaction(function () use ($payment, $gateway) {
            $now = now();

            if ($payment->status === 'approved') {
                return;
            }

            $payment->update([
                'status' => $gateway->status,

                'raw_response' => [
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
                        'transaction_id' =>
                            $gateway
                                ->transaction_details
                                ?->transaction_id,

                        'bank_transfer_id' =>
                            $gateway
                                ->transaction_details
                                ?->bank_transfer_id,
                    ],
                ],

                'paid_at' => $gateway->status === 'approved' ? now() : null,
            ]);

            $order = $payment->order;

            match ($gateway->status) {
                'approved' => $order->update([
                    'status'          => 'approved',
                    'approved_at'     => $now,
                    'ticket_number'   => $this->generateTicketNumber($order->id),
                    'expires_at'     => null,
                ]),
                'rejected',
                'cancelled' => $order->update([
                    'status' => 'cancelled',
                ]),
                default => null,
            };
        });
    }

    private function generateTicketNumber(string $orderId): string
    {
        // Busca o número sequencial da order para garantir unicidade
        $seq = Order::where('status', 'approved')->count();
        return 'VJ' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }
}