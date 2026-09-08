<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'subdomain',
        'document',
        'email',
        'phone',
        'status',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function people()
    {
        return $this->hasMany(Person::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
