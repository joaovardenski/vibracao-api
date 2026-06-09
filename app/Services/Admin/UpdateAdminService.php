<?php

namespace App\Services\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class UpdateAdminService
{
    public function execute(
        Admin $admin,
        array $data
    ): Admin {

        $admin->fill([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (!empty($data['password'])) {
            $admin->password = Hash::make(
                $data['password']
            );
        }

        $admin->save();

        return $admin->fresh();
    }
}