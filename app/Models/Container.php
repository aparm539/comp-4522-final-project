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
        'location_id',
    ];

    // Cast fields to appropriate data types
    protected $casts = [
        'quantity' => 'double',
        'ishazardous' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($container) {
            if (empty($container->barcode)) {
                do {
                    // Generate a barcode with the format MRUC****** (6 random digits)
                    $barcode = 'MRUC' . str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                } while (self::where('barcode', $barcode)->exists()); // Ensure unique barcode

                $container->barcode = $barcode;
            }
        });
    }


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
        return $this->belongsTo(Chemical::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function reconciliation_item(): HasMany
    {
        return $this->hasMany(ReconciliationItem::class, 'container_id');
    }

}
