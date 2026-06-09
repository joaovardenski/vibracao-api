<?php

namespace App\Services\Admin;

use App\Models\Admin;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeleteAdminService
{
    public function execute(Admin $admin): void
    {
        if ($admin->getKey() === Auth::id()) {
            throw new DomainException(
                'Você não pode remover sua própria conta.'
            );
        }

        DB::transaction(function () use ($admin) {
            // Trava o registro antes de contar
            Admin::lockForUpdate()->count();

            if (Admin::count() <= 1) {
                throw new DomainException(
                    'O sistema deve possuir ao menos um administrador.'
                );
            }

            $admin->delete();
        });
    }
}