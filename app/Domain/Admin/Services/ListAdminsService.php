<?php

namespace App\Domain\Admin\Services;

use App\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListAdminsService
{
    public function execute(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return Admin::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate($perPage);
    }
}