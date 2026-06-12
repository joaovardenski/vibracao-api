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
            // 1. Buscamos TODOS os admins aplicando o Lock nas linhas (o Postgres aceita aqui)
            $allAdmins = Admin::lockForUpdate()->get();

            // 2. Contamos o resultado direto na Collection do Laravel (sem tocar no banco de novo)
            if ($allAdmins->count() <= 1) {
                throw new DomainException(
                    'O sistema deve possuir ao menos um administrador.'
                );
            }

            $admin->delete();
        });
    }
}