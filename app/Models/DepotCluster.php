<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepotCluster extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'owner_id',
        'name',
        'code',
        'description',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function owner()
    {
        return $this->belongsTo(MaterialOwner::class, 'owner_id');
    }

    public function depots()
    {
        return $this->hasMany(Depot::class, 'cluster_id');
    }
}
