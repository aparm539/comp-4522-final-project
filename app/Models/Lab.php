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
 * @property string room_number
 * @property string description
 */
class Lab extends Model
{
    use HasFactory;
    public $timestamps = false;



    public function containers(): HasMany
    {
        return $this->hasMany(Container::class, 'lab_id');
    }

    public function storageLocation(): HasMany
    {
        return $this->hasMany(StorageLocation::class, 'lab_id');
    }

    public function reconciliations(): HasMany
    {
        return $this->hasMany(Reconciliation::class, 'lab_id');
    }

    public function hasOngoingReconciliation(): bool
    {
        return $this->reconciliations()->where('status', 'ongoing')->exists();
    }
}
