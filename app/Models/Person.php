<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Person extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_id',
        'city_id',
        'gender_id',
        'person_type',
        'name',
        'trade_name',
        'document_number',
        'rg_ie',
        'birth_date',
        'is_employee',
        'is_supplier',
        'is_client',
        'is_requester',
        'email',
        'phone',
        'whatsapp',
        'postal_code',
        'street',
        'number',
        'complement',
        'neighborhood',
        'commercial_same_as_residential',
        'commercial_postal_code',
        'commercial_street',
        'commercial_number',
        'commercial_complement',
        'commercial_neighborhood',
        'commercial_city_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_employee' => 'boolean',
        'is_supplier' => 'boolean',
        'is_client' => 'boolean',
        'is_requester' => 'boolean',
        'commercial_same_as_residential' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function commercialCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'commercial_city_id');
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    public function setBirthDateAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['birth_date'] = null;
            return;
        }

        if (is_string($value)) {
            // Formato brasileiro DD/MM/AAAA
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', trim($value), $matches)) {
                $this->attributes['birth_date'] = "{$matches[3]}-{$matches[2]}-{$matches[1]}";
                return;
            }
            // Formato ISO ou datetime
            try {
                $this->attributes['birth_date'] = \Carbon\Carbon::parse($value)->format('Y-m-d');
                return;
            } catch (\Exception $e) {
                // fallback
            }
        }

        $this->attributes['birth_date'] = $value;
    }

    public function getBirthDateFormattedAttribute(): ?string
    {
        return $this->birth_date ? \Carbon\Carbon::parse($this->birth_date)->format('d/m/Y') : null;
    }

}
