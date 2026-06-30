<?php

namespace App\Repositories;

use App\Contracts\Repositories\MineTypeRepositoryInterface;
use App\Models\MineType;
use Illuminate\Database\Eloquent\Builder;

class MineTypeRepository extends BaseRepository implements MineTypeRepositoryInterface
{
    public function __construct(MineType $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->applyFilters($this->model->newQuery(), $filters)
            ->orderBy('name_en')
            ->paginate($perPage)
            ->withQueryString();
    }

    protected function applyFilters($query, array $filters): Builder
    {
        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function (Builder $builder) use ($search) {
                foreach (['name_en', 'name_ps', 'description'] as $column) {
                    $builder->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }
}
