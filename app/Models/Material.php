<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_id',
        'unit_id',
        'code',
        'name',
        'description',
        'category',
        'tracking_type',
        'has_serial',
        'track_batch',
        'unit_cost',
        'min_stock',
        'is_active',
    ];

    protected $casts = [
        'has_serial' => 'boolean',
        'track_batch' => 'boolean',
        'tracking_type' => 'string',
        'is_active' => 'boolean',
        'unit_cost' => 'decimal:2',
        'min_stock' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $model) {
            $rawType = strtoupper(trim((string) $model->tracking_type));

            if (in_array($rawType, ['SERIAL', 'SERIALIZADO'], true)) {
                $model->tracking_type = 'SERIAL';
                $model->has_serial = true;
                $model->track_batch = false;
            } elseif (in_array($rawType, ['BATCH', 'LOTE', 'METRAGEM'], true)) {
                $model->tracking_type = 'BATCH';
                $model->has_serial = false;
                $model->track_batch = true;
            } elseif (in_array($rawType, ['BULK', 'GRANEL', 'CONVENCIONAL'], true)) {
                $model->tracking_type = 'BULK';
                $model->has_serial = false;
                $model->track_batch = false;
            } elseif ($model->track_batch) {
                $model->tracking_type = 'BATCH';
                $model->has_serial = false;
                $model->track_batch = true;
            } elseif ($model->has_serial) {
                $model->tracking_type = 'SERIAL';
                $model->has_serial = true;
                $model->track_batch = false;
            } else {
                $model->tracking_type = 'BULK';
                $model->has_serial = false;
                $model->track_batch = false;
            }
        });
    }

    public function isSerialized(): bool
    {
        return (bool) $this->has_serial || $this->tracking_type === 'SERIAL';
    }

    public function isBatchTracked(): bool
    {
        return (bool) $this->track_batch || $this->tracking_type === 'BATCH';
    }

    public function isBulk(): bool
    {
        return !$this->isSerialized() && !$this->isBatchTracked();
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function balances()
    {
        return $this->hasMany(StockBalance::class);
    }

    public function serials()
    {
        return $this->hasMany(StockSerial::class);
    }

    public function ownerMaterials()
    {
        return $this->hasMany(OwnerMaterial::class, 'material_id');
    }
}
