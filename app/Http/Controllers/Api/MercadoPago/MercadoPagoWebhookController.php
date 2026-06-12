<?php

namespace App\Http\Controllers\Api\MercadoPago;

use App\Domain\MercadoPago\Services\HandlePaymentWebhookService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MercadoPagoWebhookController extends Controller
{
    public function __construct(
        private readonly HandlePaymentWebhookService $webhookService
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $this->webhookService->execute($request->all());

        return response()->json([
            'status' => 'ok'
        ]);
    }
}