<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\MineAsset\StoreMineAssetRequest;
use App\Http\Requests\MineAsset\UpdateMineAssetRequest;
use App\Http\Resources\MineAssetResource;
use App\Models\MineAsset;
use App\Services\MineAssetService;
use App\Support\ActiveFinancialYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MineAssetController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private MineAssetService $service)
    {
        $this->registerModulePermissions('mine-assets');
    }

    public function index(Request $request): Response
    {
        $assets = $this->service->paginate($request->only(['search', 'date_from', 'date_to', 'status']));

        return Inertia::render('MineAssets/Index', [
            'assets' => MineAssetResource::collection($assets),
            'filters' => $request->only(['search', 'date_from', 'date_to', 'status']),
        ]);
    }

    public function create(): Response|RedirectResponse
    {
        if (ActiveFinancialYear::isAllYearsMode()) {
            return redirect()
                ->route('mine-assets.index')
                ->with('error', __('erp.financial_years.all_years_read_only'));
        }

        if (! ActiveFinancialYear::activeYear()) {
            return redirect()
                ->route('mine-assets.index')
                ->with('error', __('erp.financial_years.no_active_year'));
        }

        return Inertia::render('MineAssets/Create');
    }

    public function store(StoreMineAssetRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('mine-assets.index')->with('success', __('erp.mine_assets.created'));
    }

    public function show(MineAsset $mineAsset): Response
    {
        $mineAsset->load('creator');

        return Inertia::render('MineAssets/Show', [
            'asset' => new MineAssetResource($mineAsset),
        ]);
    }

    public function edit(MineAsset $mineAsset): Response
    {
        return Inertia::render('MineAssets/Edit', [
            'asset' => new MineAssetResource($mineAsset),
        ]);
    }

    public function update(UpdateMineAssetRequest $request, MineAsset $mineAsset): RedirectResponse
    {
        $this->service->update($mineAsset, $request->validated());

        return redirect()->route('mine-assets.index')->with('success', __('erp.mine_assets.updated'));
    }

    public function destroy(MineAsset $mineAsset): RedirectResponse
    {
        $this->service->delete($mineAsset);

        return redirect()->route('mine-assets.index')->with('success', __('erp.mine_assets.deleted'));
    }
}
