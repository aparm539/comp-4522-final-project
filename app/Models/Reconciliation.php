<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reconciliation extends Model
{
    public function ReconciliationItem(): HasMany
    {
        return $this->hasMany(ReconciliationItem::class, 'reconciliation_id');
    }

    public function storageCabinet(): BelongsTo
    {
        return $this->belongsTo(StorageCabinet::class, 'storage_cabinet_id');
    }
}
