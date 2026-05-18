<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'participants';

    protected $fillable = [
        'full_name',
        'cpf',
        'email',
        'phone',
        'city',
        'parish',
        'emergency_contact',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}