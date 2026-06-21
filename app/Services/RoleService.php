<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Role::query()->with('permissions');

        if (! empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('name')->paginate($perPage)->withQueryString();
    }

    public function find(int $id): ?Role
    {
        return Role::query()->with('permissions')->find($id);
    }

    public function allPermissions(): Collection
    {
        return Permission::query()->orderBy('name')->get();
    }

    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $permissions = $data['permissions'] ?? [];
            unset($data['permissions']);

            $role = Role::query()->create($data);

            if ($permissions !== []) {
                $role->syncPermissions($permissions);
            }

            return $role->load('permissions');
        });
    }

    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            $permissions = $data['permissions'] ?? null;
            unset($data['permissions']);

            $role->update($data);

            if ($permissions !== null) {
                $role->syncPermissions($permissions);
            }

            return $role->fresh(['permissions']);
        });
    }

    public function delete(Role $role): bool
    {
        if ($role->name === 'Super Admin') {
            throw new \InvalidArgumentException(__('erp.roles.cannot_delete_super_admin'));
        }

        return DB::transaction(fn () => (bool) $role->delete());
    }
}
