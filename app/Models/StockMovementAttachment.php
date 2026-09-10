<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovementAttachment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'account_id',
        'stock_movement_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'description',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($attachment) {
            if (!$attachment->created_at) {
                $attachment->created_at = now();
            }
        });
    }

    protected $appends = [
        'url',
    ];

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        return asset('storage/' . ltrim($this->file_path, '/'));
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function movement()
    {
        return $this->belongsTo(StockMovement::class, 'stock_movement_id');
    }
}
