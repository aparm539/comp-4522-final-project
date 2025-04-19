<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Builder
 */
class ReconciliationItem extends Model
{
    public $timestamps = false;
    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class, 'container_id');
    }

    public function reconciliation(): BelongsTo
    {
        return $this->belongsTo(Reconciliation::class, 'reconciliation_id');
    }
}
