<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait General
{
    /**
     * Scope to filter active records.
     *
     * Usage: Model::active()->get();
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Check if the current instance is active.
     *
     * Usage: $model->isActive()
     */
    public function isActive(): bool
    {
        return (bool) $this->status;
    }

    /**
     * Check if the current instance is inactive.
     *
     * Usage: $model->isInactive()
     */
    public function isInactive(): bool
    {
        return !$this->isActive();
    }
}
