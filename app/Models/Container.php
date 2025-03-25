<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'location_id',
        'shelf_id',
        'ishazardous',
        'supervisor_id',
    ];

    // Cast fields to appropriate data types
    protected $casts = [
        'quantity' => 'double',
        'ishazardous' => 'boolean',
    ];

    // Define relationships
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function unitOfMeasure()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'unit_of_measure_id');
    }
    public function shelf()
    {
        return $this->belongsTo(Shelf::class, 'shelf_id');
    }
    public function chemical()
    {
        return $this->belongsTo(Chemical::class, 'chemical_id');
    }

}
