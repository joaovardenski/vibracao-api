<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketLot extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ticket_lots';

    protected $fillable = [
        'name',
        'price',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public static function current()
    {
        return self::query()
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->firstOrFail();
    }
}
