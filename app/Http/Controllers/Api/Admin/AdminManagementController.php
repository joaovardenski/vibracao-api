<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Admin\Actions\CreateAdminAction;
use App\Domain\Admin\Actions\DeleteAdminAction;
use App\Domain\Admin\Actions\UpdateAdminAction;
use App\Domain\Admin\Services\ListAdminsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminManagementController extends Controller
{
    public function __construct(
        private readonly ListAdminsService $listAdmins,
        private readonly CreateAdminAction $createAdmin,
        private readonly UpdateAdminAction $updateAdmin,
        private readonly DeleteAdminAction $deleteAdmin,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $admins = $this->listAdmins->execute(
            perPage: $request->integer('per_page', 15),
            search: $request->input('search')
        );

        return response()->json([
            'success' => true,
            'data' => $admins,
        ]);
    }

    public function store(CreateAdminRequest $request): JsonResponse
    {
        $admin = $this->createAdmin->execute($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Administrador criado com sucesso.',
            'data' => $admin,
        ], 201);
    }

    public function show(Admin $admin): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $admin,
        ]);
    }

    public function update(UpdateAdminRequest $request, Admin $admin): JsonResponse
    {
        $updatedAdmin = $this->updateAdmin->execute($admin, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Administrador atualizado com sucesso.',
            'data' => $updatedAdmin,
        ]);
    }

    public function destroy(Admin $admin): JsonResponse
    {
        $this->deleteAdmin->execute($admin);

        return response()->json([
            'success' => true,
            'message' => 'Administrador removido com sucesso.',
        ]);
    }
}