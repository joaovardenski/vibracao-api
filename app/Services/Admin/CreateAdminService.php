<?php

namespace App\Services\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class CreateAdminService
{
    public function execute(array $data): Admin
    {
        return Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],

            'password' => Hash::make(
                $data['password']
            ),
        ]);
    }
}