<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder
 */
class Location extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function containers(): HasMany
    {
        return $this->hasMany(Container::class, 'location_id');
    }

    public function storageCabinets(): HasMany
    {
        return $this->hasMany(StorageCabinet::class, 'location_id');
    }

    public function reconciliations(): HasMany
    {
        return $this->hasMany(Reconciliation::class, 'location_id');
    }

    public function hasOngoingReconciliation(): bool
    {
        return $this->reconciliations()->where('status', 'ongoing')->exists();
    }
}
