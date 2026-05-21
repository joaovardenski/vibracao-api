<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciais inválidas.',
            ], 401);
        }

        $admin = Auth::user();

        /** @var \App\Models\Admin $admin*/
        $admin->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso.',
            'token'   => $admin->createToken('api-token')->plainTextToken,
            'admin'   => $admin,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'admin' => $request->user(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso.',
        ]);
    }
}