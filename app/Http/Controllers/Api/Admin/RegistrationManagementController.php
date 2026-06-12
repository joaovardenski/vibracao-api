<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Registration\Services\CreateManualRegistrationService;
use App\Domain\Registration\Services\ExportRegistrationsPdfService;
use App\Domain\Registration\Services\ListRegistrationsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateManualRegistrationRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationManagementController extends Controller
{
    public function __construct(
        private readonly ListRegistrationsService $listRegistrations,
        private readonly CreateManualRegistrationService $createManualRegistration,
        private readonly ExportRegistrationsPdfService $exportRegistrationsPdf,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $registrations = $this->listRegistrations->execute(
            perPage: $request->integer('per_page', 15),
            search: $request->input('search')
        );

        return response()->json([
            'success' => true,
            'data' => $registrations,
        ]);
    }

    public function store(CreateManualRegistrationRequest $request): JsonResponse
    {
        $registration = $this->createManualRegistration->execute($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Inscrição criada com sucesso.',
            'data' => $registration,
        ], 201);
    }

    public function show(Order $order): JsonResponse
    {
        $order->loadMissing(['participant', 'ticketLot', 'payment']);

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    public function exportPdf(): Response
    {
        return $this->exportRegistrationsPdf->execute();
    }
}