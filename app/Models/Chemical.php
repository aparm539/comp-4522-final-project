<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Builder
 */
class Chemical extends Model
{
    use HasFactory;

    public function containers(): HasMany
    {
        return $this->hasMany(Container::class, 'chemical_id');
    }
}
