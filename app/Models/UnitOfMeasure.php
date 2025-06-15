<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder
 *
 * @property int id
 * @property string name
 * @property string abbreviation
 */
class UnitOfMeasure extends Model
{
    use HasFactory;

    // DB table name does not conform to Eloquent's expected value,
    // so we must explicitly define it here.
    // https://laravel.com/docs/12.x/eloquent#table-names
    protected $table = 'unitsofmeasure';

    public $timestamps = false;

    // Define relationships
    public function containers(): HasMany
    {
        return $this->hasMany(Container::class, 'unit_of_measure');
    }
}
