<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class Person extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $table = 'people';

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
        'status',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_employee' => 'boolean',
        'is_supplier' => 'boolean',
        'is_client' => 'boolean',
        'is_requester' => 'boolean',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    // Scopes de Personas
    public function scopeEmployees($query)
    {
        return $query->where('is_employee', true);
    }

    public function scopeSuppliers($query)
    {
        return $query->where('is_supplier', true);
    }

    public function scopeClients($query)
    {
        return $query->where('is_client', true);
    }

    public function scopeRequesters($query)
    {
        return $query->where('is_requester', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        $cleanedTerm = preg_replace('/\D/', '', $term);

        return $query->where(function ($q) use ($term, $cleanedTerm) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('trade_name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('whatsapp', 'like', "%{$term}%");

            if (!empty($cleanedTerm)) {
                $q->orWhere('document_number', 'like', "%{$cleanedTerm}%");
            }
        });
    }
}
