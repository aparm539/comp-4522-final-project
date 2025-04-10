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
        'room_number',
        'barcode',
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

    public function storageCabinets()
    {
        return $this->hasMany(StorageCabinet::class, 'location_id');
    }

    public function reconciliations()
    {
        return $this->hasMany(Reconciliation::class, 'location_id');
    }

    public function hasOngoingReconciliation()
    {
        return $this->reconciliations()->where('status', 'ongoing')->exists();
    }
}
