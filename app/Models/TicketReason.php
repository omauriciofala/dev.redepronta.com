<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketReason extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'category_id',
        'name',
        'default_priority',
        'default_sla_hours',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_sla_hours' => 'integer',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'reason_id');
    }
}
