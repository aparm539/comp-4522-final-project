<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder
 * @property int id
 * @property string cas
 * @property string name
 * @property int whmis_hazard_class_id
 */
class Chemical extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'cas',
        'name',
        'whmis_hazard_class_id'
    ];

    public function containers(): HasMany
    {
        return $this->hasMany(Container::class, 'chemical_id');
    }

    public function whmisHazardClass(): BelongsTo
    {
        return $this->belongsTo(WhmisHazardClass::class);
    }
}
