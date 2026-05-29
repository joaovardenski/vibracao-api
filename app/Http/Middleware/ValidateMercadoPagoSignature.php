<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class ValidateMercadoPagoSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $xSignature = $request->header('x-signature');
        $xRequestId = $request->header('x-request-id');

        if (!$xSignature || !$xRequestId) {
            return response()->json(['error' => 'Missing signature headers'], 401);
        }

        // Extrai data.id da query string sem usar parse_str
        // (parse_str converte pontos para underscore)
        preg_match('/data\.id=([^&]+)/', $request->server('QUERY_STRING'), $matches);
        $dataId = $matches[1] ?? null;

        // Extrai ts e v1 do header x-signature
        $ts   = null;
        $hash = null;

        foreach (explode(',', $xSignature) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, null);
            if ($key === 'ts') $ts   = $value;
            if ($key === 'v1') $hash = $value;
        }

        if (!$ts || !$hash) {
            return response()->json(['error' => 'Invalid signature format'], 401);
        }

        // Monta o manifest — omite partes ausentes conforme doc do MP
        $parts = [];
        if ($dataId)     $parts[] = "id:{$dataId}";
        if ($xRequestId) $parts[] = "request-id:{$xRequestId}";
        if ($ts)         $parts[] = "ts:{$ts}";

        $manifest = implode(';', $parts) . ';';
        $secret   = config('services.mercado_pago.webhook_secret');
        $expected = hash_hmac('sha256', $manifest, $secret);

        if (!hash_equals($expected, $hash)) {
            Log::warning('Webhook com assinatura inválida', [
                'manifest'       => $manifest,
                'hash_received'  => $hash,
                'hash_expected'  => $expected,
                'secret_length'  => strlen($secret),
            ]);

            return response()->json(['error' => 'Invalid signature'], 401);
        }

        return $next($request);
    }
}