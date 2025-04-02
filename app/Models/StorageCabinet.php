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
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
    public function containers()
    {
        return $this->hasMany(Container::class, 'storage_cabinet_id');
    }

}
