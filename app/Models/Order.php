<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'orders';

    protected $fillable = [
        'participant_id',
        'ticket_lot_id',
        'ticket_number',
        'status',
        'amount',
        'expires_at',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function ticketLot(): BelongsTo
    {
        return $this->belongsTo(TicketLot::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}