<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'account_id',
        'material_id',
        'source_depot_id',
        'destination_depot_id',
        'user_id',
        'movement_type',
        'quantity',
        'document_ref',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function sourceDepot()
    {
        return $this->belongsTo(Depot::class, 'source_depot_id');
    }

    public function destinationDepot()
    {
        return $this->belongsTo(Depot::class, 'destination_depot_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serials()
    {
        return $this->belongsToMany(StockSerial::class, 'stock_movement_serials', 'stock_movement_id', 'stock_serial_id');
    }
}
