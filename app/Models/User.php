<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    protected $fillable = ['workshop_id', 'name', 'phone', 'rank', 'more_information', 'email', 'password'];

    protected $hidden = ['password'];

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }
}
