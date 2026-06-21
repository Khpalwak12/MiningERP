<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private UserService $service)
    {
        $this->registerModulePermissions('users');
    }

    public function index(Request $request): Response
    {
        $users = $this->service->paginate($request->only(['search']));

        return Inertia::render('Users/Index', [
            'users' => UserResource::collection($users),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Users/Create', [
            'roles' => RoleResource::collection(Role::query()->orderBy('name')->get()),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('users.index')->with('success', __('erp.users.created'));
    }

    public function show(User $user): Response
    {
        $user = $this->service->find($user->id);

        return Inertia::render('Users/Show', [
            'user' => new UserResource($user),
        ]);
    }

    public function edit(User $user): Response
    {
        $user = $this->service->find($user->id);

        return Inertia::render('Users/Edit', [
            'user' => new UserResource($user),
            'roles' => RoleResource::collection(Role::query()->orderBy('name')->get()),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->service->update($user, $request->validated());

        return redirect()->route('users.index')->with('success', __('erp.users.updated'));
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => __('erp.users.cannot_delete_self')]);
        }

        $this->service->delete($user);

        return redirect()->route('users.index')->with('success', __('erp.users.deleted'));
    }
}
