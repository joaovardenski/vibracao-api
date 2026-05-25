<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegistrationRequest;
use App\Services\CreateRegistrationService;

class RegistrationController extends Controller
{
    public function store(RegistrationRequest $request, CreateRegistrationService $service) {
        $result = $service->execute($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Inscrição iniciada.',
            'data' => $result,
        ], 201);
    }
}
