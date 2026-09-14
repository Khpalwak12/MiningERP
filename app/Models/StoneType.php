<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StoneType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_en', 'name_ps', 'description', 'slug'];

    protected $appends = ['localized_name'];

    public function shipments(): HasMany
    {
        return $this->hasMany(MarbleShipment::class);
    }

    public static function listForSelect(): Collection
    {
        $locale = app()->getLocale();
        $nameColumn = $locale === 'ps' ? 'name_ps' : 'name_en';

        return static::query()
            ->orderBy($nameColumn)
            ->orderBy('name_en')
            ->get();
    }

    protected static function booted(): void
    {
        static::saving(function (self $stoneType) {
            if ($stoneType->name_en) {
                $stoneType->name = $stoneType->name_en;
            }

            if (! $stoneType->slug && $stoneType->name_en) {
                $base = Str::slug($stoneType->name_en);
                $slug = $base;
                $counter = 1;

                while (static::query()
                    ->where('slug', $slug)
                    ->when($stoneType->exists, fn ($q) => $q->where('id', '!=', $stoneType->id))
                    ->exists()) {
                    $slug = $base.'-'.$counter++;
                }

                $stoneType->slug = $slug;
            }
        });
    }

    protected function localizedName(): Attribute
    {
        return Attribute::get(function () {
            if (app()->getLocale() === 'ps' && filled($this->name_ps)) {
                return $this->name_ps;
            }

            return $this->name_en ?: $this->name;
        });
    }
}
