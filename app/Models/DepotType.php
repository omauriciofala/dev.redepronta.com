<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepotType extends Model
{
    protected $fillable = [
        'account_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function depots()
    {
        return $this->hasMany(Depot::class, 'type', 'code');
    }
}
