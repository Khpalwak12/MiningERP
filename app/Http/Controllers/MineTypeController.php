<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\MineType\StoreMineTypeRequest;
use App\Http\Requests\MineType\UpdateMineTypeRequest;
use App\Http\Resources\MineTypeResource;
use App\Models\MineType;
use App\Services\MineTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MineTypeController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private MineTypeService $service)
    {
        $this->registerModulePermissions('mine-types');
    }

    public function index(Request $request): Response
    {
        $mineTypes = $this->service->paginate($request->only(['search']));

        return Inertia::render('MineTypes/Index', [
            'mineTypes' => MineTypeResource::collection($mineTypes),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('MineTypes/Create');
    }

    public function store(StoreMineTypeRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('mine-types.index')
            ->with('success', __('erp.mine_types.created'));
    }

    public function edit(MineType $mineType): Response
    {
        return Inertia::render('MineTypes/Edit', [
            'mineType' => new MineTypeResource($mineType),
        ]);
    }

    public function update(UpdateMineTypeRequest $request, MineType $mineType): RedirectResponse
    {
        $this->service->update($mineType, $request->validated());

        return redirect()->route('mine-types.index')
            ->with('success', __('erp.mine_types.updated'));
    }

    public function destroy(MineType $mineType): RedirectResponse
    {
        try {
            $this->service->delete($mineType);
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('mine-types.index')->with('error', $e->getMessage());
        }

        return redirect()->route('mine-types.index')
            ->with('success', __('erp.mine_types.deleted'));
    }
}
