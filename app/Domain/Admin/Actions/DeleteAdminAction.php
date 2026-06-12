<?php

namespace App\Domain\Admin\Actions;

use App\Models\Admin;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeleteAdminAction
{
    public function execute(Admin $admin): void
    {
        if ($admin->getKey() === Auth::id()) {
            throw new DomainException('Você não pode remover sua própria conta.');
        }

        DB::transaction(function () use ($admin) {
            $allAdmins = Admin::lockForUpdate()->get();

            if ($allAdmins->count() <= 1) {
                throw new DomainException('O sistema deve possuir ao menos um administrador.');
            }

            $admin->delete();
        });
    }
}