<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\StoneType\StoreStoneTypeRequest;
use App\Http\Requests\StoneType\UpdateStoneTypeRequest;
use App\Http\Resources\StoneTypeResource;
use App\Models\StoneType;
use App\Services\StoneTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StoneTypeController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private StoneTypeService $service)
    {
        $this->registerModulePermissions('stone-types');
    }

    public function index(Request $request): Response
    {
        $stoneTypes = $this->service->paginate($request->only(['search']));

        return Inertia::render('StoneTypes/Index', [
            'stoneTypes' => StoneTypeResource::collection($stoneTypes),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('StoneTypes/Create');
    }

    public function store(StoreStoneTypeRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('stone-types.index')
            ->with('success', __('erp.stone_types.created'));
    }

    public function edit(StoneType $stoneType): Response
    {
        return Inertia::render('StoneTypes/Edit', [
            'stoneType' => new StoneTypeResource($stoneType),
        ]);
    }

    public function update(UpdateStoneTypeRequest $request, StoneType $stoneType): RedirectResponse
    {
        $this->service->update($stoneType, $request->validated());

        return redirect()->route('stone-types.index')
            ->with('success', __('erp.stone_types.updated'));
    }

    public function destroy(StoneType $stoneType): RedirectResponse
    {
        try {
            $this->service->delete($stoneType);
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('stone-types.index')->with('error', $e->getMessage());
        }

        return redirect()->route('stone-types.index')
            ->with('success', __('erp.stone_types.deleted'));
    }
}
