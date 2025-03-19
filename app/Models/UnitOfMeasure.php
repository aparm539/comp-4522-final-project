<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitOfMeasure extends Model
{
    use HasFactory;
    protected $table = 'unitsofmeasure';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    // Define fillable fields for mass assignment
    protected $fillable = [
        'measure_name',
        'abbreviation',
    ];

    // Define relationships
    public function containers()
    {
        return $this->hasMany(Container::class, 'unit_of_measure');
    }
}
