<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'origin_task_id',
        'protocol',
        'title',
        'description',
        'customer_person_id',
        'requester_person_id',
        'city_id',
        'department_id',
        'category_id',
        'reason_id',
        'priority',
        'status',
        'address_street',
        'address_number',
        'address_neighborhood',
        'address_postal_code',
        'latitude',
        'longitude',
        'sla_due_at',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'sla_due_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function originTask()
    {
        return $this->belongsTo(Task::class, 'origin_task_id');
    }

    public function customerPerson()
    {
        return $this->belongsTo(Person::class, 'customer_person_id');
    }

    public function requesterPerson()
    {
        return $this->belongsTo(Person::class, 'requester_person_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function reason()
    {
        return $this->belongsTo(TicketReason::class, 'reason_id');
    }

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class);
    }
}
