<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ExpenseCategory extends Model
{
    protected $fillable = ['name', 'name_en', 'name_ps', 'description', 'slug', 'parent_id'];

    protected $appends = ['localized_name'];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public static function listForSelect(): Collection
    {
        $locale = app()->getLocale();
        $nameColumn = $locale === 'ps' ? 'name_ps' : 'name_en';

        return static::query()
            ->whereNull('parent_id')
            ->orderBy($nameColumn)
            ->orderBy('name_en')
            ->get();
    }

    public static function idsMatchingSearch(string $search): array
    {
        $needle = mb_strtolower(trim($search));

        return static::query()
            ->whereNull('parent_id')
            ->get()
            ->filter(function (self $category) use ($needle) {
                return str_contains(mb_strtolower($category->localized_name), $needle)
                    || str_contains(mb_strtolower($category->name_en ?? ''), $needle)
                    || str_contains(mb_strtolower($category->name_ps ?? ''), $needle)
                    || str_contains(mb_strtolower($category->description ?? ''), $needle);
            })
            ->pluck('id')
            ->all();
    }

    protected static function booted(): void
    {
        static::saving(function (self $category) {
            if ($category->name_en) {
                $category->name = $category->name_en;
            }

            if (! $category->slug && $category->name_en) {
                $base = Str::slug($category->name_en);
                $slug = $base;
                $counter = 1;

                while (static::query()
                    ->where('slug', $slug)
                    ->when($category->exists, fn ($q) => $q->where('id', '!=', $category->id))
                    ->exists()) {
                    $slug = $base.'-'.$counter++;
                }

                $category->slug = $slug;
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
