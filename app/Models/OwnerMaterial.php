<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OwnerMaterial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'material_owner_id',
        'material_id',
        'owner_code',
        'owner_name',
        'notes',
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
        return $this->belongsTo(MaterialOwner::class, 'material_owner_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
