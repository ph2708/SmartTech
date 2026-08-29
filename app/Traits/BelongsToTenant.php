<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $query) {
            if (session()->has('tenant_id')) {
                $query->where($query->getModel()->getTable() . '.tenant_id', session('tenant_id'));
            }
        });

        static::creating(function ($model) {
            if (session()->has('tenant_id') && !$model->tenant_id) {
                $model->tenant_id = session('tenant_id');
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
