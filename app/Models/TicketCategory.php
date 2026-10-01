<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'department_id',
        'name',
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

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function reasons()
    {
        return $this->hasMany(TicketReason::class, 'category_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }
}
