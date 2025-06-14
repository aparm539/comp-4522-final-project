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
 * @property int storage_location_id
 * @property int last_edit_author_id
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

    public function storageLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class, 'storage_location_id');
    }

    public function chemical(): BelongsTo
    {
        return $this->belongsTo(Chemical::class);
    }

    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }

    public function reconciliationItems(): HasMany
    {
        return $this->hasMany(ReconciliationItem::class, 'container_id');
    }

    public function lastEditAuthor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_edit_author_id');
    }
}
