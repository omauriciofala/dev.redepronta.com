<?php

namespace App\Traits;

use App\Models\Account;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::creating(function ($model) {
            if (empty($model->account_id)) {
                if (auth()->check() && !empty(auth()->user()->account_id)) {
                    $model->account_id = auth()->user()->account_id;
                } elseif (session()->has('current_account_id')) {
                    $model->account_id = session('current_account_id');
                } else {
                    $defaultAccount = Account::first();
                    if ($defaultAccount) {
                        $model->account_id = $defaultAccount->id;
                    }
                }
            }
        });

        static::addGlobalScope('account', function (Builder $builder) {
            if (auth()->check() && !empty(auth()->user()->account_id)) {
                $builder->where($builder->getModel()->getTable() . '.account_id', auth()->user()->account_id);
            } elseif (session()->has('current_account_id')) {
                $builder->where($builder->getModel()->getTable() . '.account_id', session('current_account_id'));
            }
        });
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
