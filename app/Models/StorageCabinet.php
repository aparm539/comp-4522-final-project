<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder
 * @property int id
 * @property DateTime created_at
 * @property DateTime updated_at
 * @property string barcode
 * @property string name
 * @property int location_id
 */
class StorageCabinet extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function containers(): HasMany
    {
        return $this->hasMany(Container::class, 'storage_cabinet_id');
    }
}
