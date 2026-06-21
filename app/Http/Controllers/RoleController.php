<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private RoleService $service)
    {
        $this->registerModulePermissions('roles');
    }

    public function index(Request $request): Response
    {
        $roles = $this->service->paginate($request->only(['search']));

        return Inertia::render('Roles/Index', [
            'roles' => RoleResource::collection($roles),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Roles/Create', [
            'permissions' => $this->service->allPermissions()->pluck('name')->values()->all(),
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('roles.index')->with('success', __('erp.roles.created'));
    }

    public function show(Role $role): Response
    {
        $role = $this->service->find($role->id);

        return Inertia::render('Roles/Show', [
            'role' => new RoleResource($role),
        ]);
    }

    public function edit(Role $role): Response
    {
        $role = $this->service->find($role->id);

        return Inertia::render('Roles/Edit', [
            'role' => new RoleResource($role),
            'permissions' => $this->service->allPermissions()->pluck('name')->values()->all(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        try {
            $this->service->update($role, $request->validated());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['role' => $e->getMessage()]);
        }

        return redirect()->route('roles.index')->with('success', __('erp.roles.updated'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        try {
            $this->service->delete($role);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['role' => $e->getMessage()]);
        }

        return redirect()->route('roles.index')->with('success', __('erp.roles.deleted'));
    }
}
