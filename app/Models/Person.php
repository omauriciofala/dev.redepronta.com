<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

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
        'registration_date',
        'group_name',

        // Papéis do Cadastro
        'is_client',
        'is_supplier',
        'is_employee',
        'is_outsourced',
        'is_seller',
        'is_driver',
        'is_carrier',
        'is_requester',

        // Contatos
        'email',
        'phone',
        'whatsapp',

        // Endereço Residencial / Principal
        'postal_code',
        'street',
        'number',
        'complement',
        'neighborhood',
        'reference',
        'latitude',
        'longitude',

        // Endereço Comercial
        'commercial_same_as_residential',
        'commercial_postal_code',
        'commercial_street',
        'commercial_number',
        'commercial_complement',
        'commercial_neighborhood',
        'commercial_reference',
        'commercial_latitude',
        'commercial_longitude',
        'commercial_city_id',

        'status',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'registration_date' => 'date',
        'is_client' => 'boolean',
        'is_supplier' => 'boolean',
        'is_employee' => 'boolean',
        'is_outsourced' => 'boolean',
        'is_seller' => 'boolean',
        'is_driver' => 'boolean',
        'is_carrier' => 'boolean',
        'is_requester' => 'boolean',
        'commercial_same_as_residential' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'commercial_latitude' => 'float',
        'commercial_longitude' => 'float',
        'rg_issue_date' => 'date',
        'birth_or_foundation_date' => 'date',
        'share_capital' => 'decimal:2',
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

    // Scopes de busca e papéis
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $clean = preg_replace('/\D/', '', $term);
        return $query->where(function ($q) use ($term, $clean) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('trade_name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('group_name', 'like', "%{$term}%");

            if (!empty($clean)) {
                $q->orWhere('document_number', 'like', "%{$clean}%");
            }
        });
    }

    public function scopeClients(Builder $query): Builder
    {
        return $query->where('is_client', true);
    }

    public function scopeSuppliers(Builder $query): Builder
    {
        return $query->where('is_supplier', true);
    }

    public function scopeEmployees(Builder $query): Builder
    {
        return $query->where('is_employee', true);
    }

    public function scopeOutsourced(Builder $query): Builder
    {
        return $query->where('is_outsourced', true);
    }

    public function scopeSellers(Builder $query): Builder
    {
        return $query->where('is_seller', true);
    }

    public function scopeDrivers(Builder $query): Builder
    {
        return $query->where('is_driver', true);
    }

    public function scopeCarriers(Builder $query): Builder
    {
        return $query->where('is_carrier', true);
    }

    public function scopeRequesters(Builder $query): Builder
    {
        return $query->where('is_requester', true);
    }

    // Mutator para birth_date (DD/MM/AAAA -> YYYY-MM-DD)
    public function setBirthDateAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['birth_date'] = null;
            return;
        }

        if (is_string($value)) {
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', trim($value), $matches)) {
                $this->attributes['birth_date'] = "{$matches[3]}-{$matches[2]}-{$matches[1]}";
                return;
            }
            try {
                $this->attributes['birth_date'] = \Carbon\Carbon::parse($value)->format('Y-m-d');
                return;
            } catch (\Exception $e) {}
        }

        $this->attributes['birth_date'] = $value;
    }

    public function getBirthDateFormattedAttribute(): ?string
    {
        return $this->birth_date ? \Carbon\Carbon::parse($this->birth_date)->format('d/m/Y') : null;
    }

    // Mutator para registration_date (DD/MM/AAAA -> YYYY-MM-DD)
    public function setRegistrationDateAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['registration_date'] = now()->format('Y-m-d');
            return;
        }

        if (is_string($value)) {
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', trim($value), $matches)) {
                $this->attributes['registration_date'] = "{$matches[3]}-{$matches[2]}-{$matches[1]}";
                return;
            }
            try {
                $this->attributes['registration_date'] = \Carbon\Carbon::parse($value)->format('Y-m-d');
                return;
            } catch (\Exception $e) {}
        }

        $this->attributes['registration_date'] = $value;
    }

    public function getRegistrationDateFormattedAttribute(): ?string
    {
        return $this->registration_date ? \Carbon\Carbon::parse($this->registration_date)->format('d/m/Y') : null;
    }
}
