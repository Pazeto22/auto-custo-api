<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
{
    protected $fillable = [
        'budget_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    // Boot method para calcular total_price automaticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($budgetItem) {
            $budgetItem->total_price = $budgetItem->quantity * $budgetItem->unit_price;
        });

        static::updating(function ($budgetItem) {
            $budgetItem->total_price = $budgetItem->quantity * $budgetItem->unit_price;
        });
    }

    // Relação com Budget
    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    // Relação com Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Método opcional para calcular o total manualmente (se precisar)
    public function calculateTotalPrice(): float
    {
        return $this->quantity * $this->unit_price;
    }
}
