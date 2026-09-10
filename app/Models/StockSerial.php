<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockSerial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'material_id',
        'current_depot_id',
        'serial_number',
        'mac_address',
        'status',
        'notes',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function currentDepot()
    {
        return $this->belongsTo(Depot::class, 'current_depot_id');
    }
}
