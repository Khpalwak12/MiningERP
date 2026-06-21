<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarbleType extends AuditableModel
{
    use SoftDeletes;

    protected $fillable = ['name', 'status'];

    public function shipments(): HasMany
    {
        return $this->hasMany(MarbleShipment::class);
    }
}
