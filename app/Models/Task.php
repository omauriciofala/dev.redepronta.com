<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'task_number',
        'title',
        'description',
        'source',
        'priority',
        'status',
        'assigned_user_id',
        'customer_person_id',
        'metadata',
        'resolved_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function customerPerson()
    {
        return $this->belongsTo(Person::class, 'customer_person_id');
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class, 'origin_task_id');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class)->orderBy('created_at', 'asc');
    }
}
