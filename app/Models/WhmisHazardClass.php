<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhmisHazardClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_name',
        'description',
        'symbol',
    ];

    public $timestamps = false;

    public function chemicals(): HasMany
    {
        return $this->hasMany(Chemical::class);
    }
} 