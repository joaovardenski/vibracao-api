<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HandlePaymentWebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MercadoPagoWebhookController extends Controller
{
    public function __construct(
        private HandlePaymentWebhookService $webhookService
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $this->webhookService->execute($request->all());

        return response()->json(['status' => 'ok']);
    }
}