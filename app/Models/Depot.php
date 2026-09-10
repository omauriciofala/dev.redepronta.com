<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Depot extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'cluster_id',
        'responsible_person_id',
        'city_id',
        'name',
        'code',
        'type',
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

    public function cluster()
    {
        return $this->belongsTo(DepotCluster::class, 'cluster_id');
    }

    public function responsiblePerson()
    {
        return $this->belongsTo(Person::class, 'responsible_person_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function balances()
    {
        return $this->hasMany(StockBalance::class);
    }

    public function serials()
    {
        return $this->hasMany(StockSerial::class, 'current_depot_id');
    }
}
