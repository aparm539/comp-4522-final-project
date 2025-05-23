<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder
 *
 * @property int id
 * @property string barcode
 * @property float quantity
 * @property int unit_of_measure_id
 * @property int chemical_id
 * @property \DateTime created_at
 * @property \DateTime updated_at
 * @property int storage_cabinet_id
 */
class Container extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function unitOfMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitOfMeasure::class, 'unit_of_measure_id');
    }

    public function storageCabinet(): BelongsTo
    {
        return $this->belongsTo(StorageCabinet::class, 'storage_cabinet_id');
    }

    public function chemical(): BelongsTo
    {
        return $this->belongsTo(Chemical::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function reconciliationItems(): HasMany
    {
        return $this->hasMany(ReconciliationItem::class, 'container_id');
    }
}
