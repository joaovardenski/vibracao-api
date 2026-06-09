<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\Admin;
use App\Services\Admin\CreateAdminService;
use App\Services\Admin\DeleteAdminService;
use App\Services\Admin\ListAdminsService;
use App\Services\Admin\UpdateAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminManagementController extends Controller
{
    public function __construct(
        private ListAdminsService $listAdmins,
        private CreateAdminService $createAdmin,
        private UpdateAdminService $updateAdmin,
        private DeleteAdminService $deleteAdmin,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->listAdmins->execute(
                perPage: $request->integer('per_page', 15),
                search: $request->input('search'),
            ),
        ]);
    }

    public function show(Admin $admin): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $admin,
        ]);
    }

    public function store(
        CreateAdminRequest $request
    ): JsonResponse {
        $admin = $this->createAdmin
            ->execute($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Administrador criado com sucesso.',
            'data' => $admin,
        ], 201);
    }

    public function update(
        UpdateAdminRequest $request,
        Admin $admin
    ): JsonResponse {
        $admin = $this->updateAdmin->execute(
            $admin,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Administrador atualizado com sucesso.',
            'data' => $admin,
        ]);
    }

    public function destroy(
        Admin $admin
    ): JsonResponse {
        $this->deleteAdmin->execute($admin);

        return response()->json([
            'success' => true,
            'message' => 'Administrador removido com sucesso.',
        ]);
    }
}