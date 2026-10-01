<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dispatch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'ticket_id',
        'worker_person_id',
        'depot_id',
        'dispatch_number',
        'status',
        'scheduled_at',
        'dispatched_at',
        'arrived_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'arrived_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function workerPerson()
    {
        return $this->belongsTo(Person::class, 'worker_person_id');
    }

    public function depot()
    {
        return $this->belongsTo(Depot::class);
    }
}
