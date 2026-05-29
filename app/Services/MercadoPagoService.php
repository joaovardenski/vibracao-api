<?php

namespace App\Services;

use Exception;
use DomainException;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;
use App\Models\Participant;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\Resources\Preference;

class MercadoPagoService
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(
            config('services.mercado_pago.access_token_prod')
        );
    }

    public function createPreference(
        string $title,
        float $price,
        string $externalReference,
        Participant $participant,
    ): Preference {
        try {
            $client = new PreferenceClient();
            return $client->create([
                'external_reference' => $externalReference,
                'items' => [[
                    'title'      => $title,
                    'description' => 'Ingresso para o evento Vibração Jovem 2026',
                    'quantity'   => 1,
                    'unit_price' => 0.01,
                ]],

                'payer' => [
                    'name' => $participant->full_name,
                    'email' => $participant->email,

                    'identification' => [
                        'type' => 'CPF',
                        'number' => $participant->cpf,
                    ],
                ],

                'payment_methods' => [
                    'default_payment_method_id' => 'pix',

                    'excluded_payment_types' => [
                        ['id' => 'credit_card'],
                        ['id' => 'debit_card'],
                        ['id' => 'prepaid_card'],
                        ['id' => 'ticket'],
                    ],
                ],

                'date_of_expiration' => now()
                    ->addMinutes(15)
                    ->toIso8601String(),

                'back_urls' => [
                    'success' => config('app.front_url') . '/payment/success',
                    'failure' => config('app.front_url') . '/payment/failure',
                    'pending' => config('app.front_url') . '/payment/pending',
                ],
                'auto_return' => 'approved',
            ]);
        } catch (MPApiException $e) {
            $response = $e->getApiResponse();
            throw new Exception(json_encode([
                'status'  => $response?->getStatusCode(),
                'content' => $response?->getContent(),
            ], JSON_PRETTY_PRINT));
        } catch (Exception $e) {
            throw new Exception('Erro Mercado Pago: ' . $e->getMessage());
        }
    }

    public function getPayment(string $paymentId): object
    {
        try {
            $client = new PaymentClient();
            return $client->get($paymentId);
        } catch (MPApiException $e) {
            $response = $e->getApiResponse();
            throw new DomainException(
                $response?->getContent()['message']
                ?? 'Erro ao buscar pagamento.'
            );
        } catch (Exception $e) {
            throw new DomainException(
                'Erro Mercado Pago.'
            );
        }
    }
}