<?php

namespace App\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workshop extends Model
{
    protected $fillable = ['name', 'phone', 'address'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
