<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder
 * @property int id
 * @property string status
 * @property string notes
 * @property DateTime started_at
 * @property DateTime ended_at
 * @property DateTime created_at
 * @property DateTime updated_at
 * @property int location_id
 */
class Reconciliation extends Model
{
    public function reconciliationItems(): HasMany
    {
        return $this->hasMany(ReconciliationItem::class, 'reconciliation_id');
    }

    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }
}
