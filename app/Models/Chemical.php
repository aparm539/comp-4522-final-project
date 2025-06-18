<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder
 *
 * @property int id
 * @property string cas
 * @property string name
 */
class Chemical extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'cas',
        'name',
    ];

    public function containers(): HasMany
    {
        return $this->hasMany(Container::class, 'chemical_id');
    }

    public function whmisHazardClasses(): BelongsToMany
    {
        return $this->belongsToMany(WhmisHazardClass::class);
    }

    /**
     * Backwards-compatibility accessor returning the first associated WHMIS hazard class.
     *
     * This allows existing code that references `$chemical->whmisHazardClass` (singular)
     * to continue to work while the application is upgraded to support multiple classes.
     */
    public function getWhmisHazardClassAttribute(): ?WhmisHazardClass
    {
        return $this->whmisHazardClasses->first();
    }
}
