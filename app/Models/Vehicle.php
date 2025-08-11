<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    protected $fillable = ['client_id', 'brand', 'model', 'year', 'license_plate'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
