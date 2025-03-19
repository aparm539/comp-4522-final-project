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
    public $timestamps = false;

    // Define fillable fields for mass assignment
    protected $fillable = [
        'barcode',
        'cas',
        'quantity',
        'unit_of_measure',
        'location_id',
        'shelf_id',
        'ishazardous',
        'date_added',
        'supervisor_id',
        'chemical_name',
        'container_name',
    ];

    // Cast fields to appropriate data types
    protected $casts = [
        'quantity' => 'double',
        'ishazardous' => 'boolean',
        'date_added' => 'date',
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
        return $this->belongsTo(UnitOfMeasure::class, 'unit_of_measure');
    }
    public function shelf()
    {
        return $this->belongsTo(Shelf::class, 'shelf_id');
    }

}
