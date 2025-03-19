<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $table = 'locations';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    // Define fillable fields for mass assignment
    protected $fillable = [
        'location_barcode',
        'shelf',
        'room_number',
        'description',
        'supervisor_id',
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function containers()
    {
        return $this->hasMany(Container::class, 'location_id');
    }
}
