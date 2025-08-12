<?php

namespace App\Models;

use App\Models\Vehicle;
use App\Models\Budget;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'workshop_id',
        'name',
        'phone',
        'email',
        'address',
        'more_information'
    ];

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }
}
