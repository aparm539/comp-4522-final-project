<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageCabinet extends Model
{
    /** @use HasFactory<\Database\Factories\StorageCabinetFactory> */
    use HasFactory;
    protected $table = 'storage_cabinets';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'location_id',
        'name',
        'barcode'
    ];

    protected static function booted()
    {
        static::saving(function ($storageCabinet) {
            if (empty($storageCabinet->barcode)) {
                do {
                    // Generate a barcode with the format MRUC****** (6 random digits)
                    $barcode = 'MRUC' . str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                } while (self::where('barcode', $barcode)->exists()); // Ensure unique barcode

                $storageCabinet->barcode = $barcode;
            }
        });
    }
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
    public function containers()
    {
        return $this->hasMany(Container::class, 'storage_cabinet_id');
    }

}
