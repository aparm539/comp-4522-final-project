<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PhpParser\Node\Expr\Cast\Double;

class Container extends Model
{
    use HasFactory;
    protected $table = 'containers';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;


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

    public function reconciliation_item(): HasMany
    {
        return $this->hasMany(ReconciliationItem::class, 'container_id');
    }

}
