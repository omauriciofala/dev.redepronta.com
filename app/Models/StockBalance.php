<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockBalance extends Model
{
    protected $fillable = [
        'account_id',
        'depot_id',
        'material_id',
        'quantity',
        'reserved_quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'reserved_quantity' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function depot()
    {
        return $this->belongsTo(Depot::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function getAvailableQuantityAttribute(): float
    {
        return max(0, (float)$this->quantity - (float)$this->reserved_quantity);
    }
}
