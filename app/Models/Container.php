<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Container extends Model
{
    use HasFactory;
    protected $table = 'containers';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    // Define fillable fields for mass assignment
    protected $fillable = [
        'barcode',
        'quantity',
        'unit_of_measure_id',
        'chemical_id',
        'shelf_id',
        'ishazardous',
        'supervisor_id',
    ];

    // Cast fields to appropriate data types
    protected $casts = [
        'quantity' => 'double',
        'ishazardous' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function unitOfMeasure()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'unit_of_measure_id');
    }
    public function storageCabinet()
    {
        return $this->belongsTo(StorageCabinet::class, 'storage_cabinet_id');
    }
    public function chemical()
    {
        return $this->belongsTo(Chemical::class, 'chemical_id');
    }

    public function reconciliation_item(): HasMany
    {
        return $this->hasMany(ReconciliationItem::class, 'container_id');
    }

}
