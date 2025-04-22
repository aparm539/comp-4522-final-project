<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder
 * @property int id
 * @property string cas
 * @property string name
 * @property bool ishazardous
 */
class Chemical extends Model
{
    use HasFactory;
    public $timestamps = false;


    public function containers(): HasMany
    {
        return $this->hasMany(Container::class, 'chemical_id');
    }
}
