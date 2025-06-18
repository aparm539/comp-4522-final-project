<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WhmisHazardClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_name',
        'description',
        'symbol',
    ];

    public $timestamps = false;

    public function chemicals(): BelongsToMany
    {
        return $this->belongsToMany(Chemical::class);
    }
}
