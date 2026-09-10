<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'unit_id',
        'code',
        'name',
        'description',
        'category',
        'has_serial',
        'unit_cost',
        'min_stock',
        'is_active',
    ];

    protected $casts = [
        'has_serial' => 'boolean',
        'is_active' => 'boolean',
        'unit_cost' => 'decimal:2',
        'min_stock' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function balances()
    {
        return $this->hasMany(StockBalance::class);
    }

    public function serials()
    {
        return $this->hasMany(StockSerial::class);
    }
}
