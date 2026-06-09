<?php

namespace App\Services\Admin;

use App\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListAdminsService
{
    public function execute(
        int $perPage = 15,
        ?string $search = null,
    ): LengthAwarePaginator {

        return Admin::query()

            ->when(
                $search,
                fn ($query) =>
                    $query->where(function ($q) use ($search) {
                        $q->whereRaw(
                            'LOWER(name) LIKE ?',
                            ['%' . strtolower($search) . '%']
                        )
                        ->orWhereRaw(
                            'LOWER(email) LIKE ?',
                            ['%' . strtolower($search) . '%']
                        );
                    })
            )

            ->orderBy('name')
            ->paginate($perPage);
    }
}