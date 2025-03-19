<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chemical extends Model
{
    /** @use HasFactory<\Database\Factories\ChemicalFactory> */
    use HasFactory;
    protected $table = 'chemicals';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'cas',
        'full_name',
        ];

    public function containers(){
        return $this->hasMany(Container::class, 'chemical_id');
    }

}
