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

    protected $casts = [
        // Casts the `symbol` column to our enum for convenient icon retrieval.
        'symbol' => \App\Enums\WhmisPictogram::class,
    ];

    public $timestamps = false;

    public function chemicals(): BelongsToMany
    {
        return $this->belongsToMany(Chemical::class);
    }

    /**
     * Convenient accessor to obtain the Heroicon name for this hazard class.
     */
    public function getIconAttribute(): string
    {
        return $this->symbol->icon();
    }
}
