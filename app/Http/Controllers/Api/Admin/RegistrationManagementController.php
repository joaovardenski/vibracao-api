<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateManualRegistrationRequest;
use App\Models\Order;
use App\Services\Admin\CreateManualRegistrationService;
use App\Services\Admin\ListRegistrationsService;
use App\Services\Admin\ExportRegistrationsPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegistrationManagementController extends Controller
{
    public function __construct(
        private ListRegistrationsService $listRegistrations,
        private CreateManualRegistrationService $createManualRegistration,
        private ExportRegistrationsPdfService $exportRegistrationsPdf,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->listRegistrations->execute(
                perPage: $request->integer('per_page', 15),
                search: $request->input('search'),
                status: $request->input('status'),
            ),
        ]);
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $order->load([
                'participant',
                'ticketLot',
                'payment',
            ]),
        ]);
    }

    public function store(
        CreateManualRegistrationRequest $request
    ): JsonResponse {
        $registration =
            $this->createManualRegistration
                ->execute($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Inscrição criada com sucesso.',
            'data' => $registration,
        ], 201);
    }

    public function exportPdf()
    {
        return $this->exportRegistrationsPdf->execute();
    }
}