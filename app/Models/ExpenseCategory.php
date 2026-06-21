<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Lang;

class ExpenseCategory extends Model
{
    protected $fillable = ['name', 'slug', 'parent_id'];

    protected $appends = ['localized_name'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public static function resolveLocalizedName(?string $slug, ?string $fallback = null): string
    {
        if ($slug && Lang::has("erp.expense_categories.{$slug}")) {
            return __("erp.expense_categories.{$slug}");
        }

        return $fallback ?? '';
    }

    protected function localizedName(): Attribute
    {
        return Attribute::get(fn () => self::resolveLocalizedName($this->slug, $this->name));
    }

    public static function parentIdsMatchingSearch(string $search): array
    {
        $needle = mb_strtolower(trim($search));

        return static::query()
            ->whereNull('parent_id')
            ->get()
            ->filter(function (self $category) use ($needle) {
                return str_contains(mb_strtolower($category->localized_name), $needle)
                    || str_contains(mb_strtolower($category->name), $needle);
            })
            ->pluck('id')
            ->all();
    }
}
