<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(
    basePath: dirname(__DIR__)
)

    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (
        Middleware $middleware
    ): void {
        $middleware->redirectGuestsTo(
            fn ($request) =>
                $request->is('api/*')
                    ? null
                    : route('login')
        );

        $middleware->alias([
            'mp.signature' => \App\Http\Middleware\ValidateMercadoPagoSignature::class,
        ]);
    })

    ->withExceptions(function (
        Exceptions $exceptions
    ): void {

        // Não autenticado
        $exceptions->render(
            function (
                AuthenticationException $e,
                $request
            ) {

                if ($request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Não autenticado.',
                    ], 401);
                }
            }
        );

        // Regras de negócio
        $exceptions->render(
            function (
                DomainException $e,
                $request
            ) {

                if ($request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ], 409);
                }
            }
        );

        // Recurso não encontrado
        $exceptions->render(
            function (
                ModelNotFoundException $e,
                $request
            ) {
                if ($request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Recurso não encontrado.',
                    ], 404);
                }
            }
        );

        // Rota não encontrada
        $exceptions->render(
            function (
                NotFoundHttpException $e,
                $request
            ) {
                if ($request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Recurso não encontrado.',
                    ], 404);
                }
            }
        );
    })

    ->create();