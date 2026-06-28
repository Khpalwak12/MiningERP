<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\ContractorProduction\StoreContractorProductionRequest;
use App\Http\Requests\ContractorProduction\UpdateContractorProductionRequest;
use App\Http\Resources\ContractorProductionResource;
use App\Models\ContractorProduction;
use App\Services\ContractorProductionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContractorProductionController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private ContractorProductionService $service)
    {
        $this->registerModulePermissions('contractor-royalty');
    }

    public function index(Request $request): Response
    {
        $productions = $this->service->paginate($request->only(['search', 'date_from', 'date_to']));

        return Inertia::render('ContractorRoyalty/Productions/Index', [
            'productions' => ContractorProductionResource::collection($productions),
            'filters' => $request->only(['search', 'date_from', 'date_to']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('ContractorRoyalty/Productions/Create', [
            'defaultRatePerTon' => $this->service->defaultRatePerTon(),
        ]);
    }

    public function store(StoreContractorProductionRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('contractor-royalty.productions.index')
            ->with('success', __('erp.contractor_royalty.production_created'));
    }

    public function show(ContractorProduction $production): Response
    {
        $production->load('creator');

        return Inertia::render('ContractorRoyalty/Productions/Show', [
            'production' => new ContractorProductionResource($production),
        ]);
    }

    public function edit(ContractorProduction $production): Response
    {
        return Inertia::render('ContractorRoyalty/Productions/Edit', [
            'production' => new ContractorProductionResource($production),
        ]);
    }

    public function update(UpdateContractorProductionRequest $request, ContractorProduction $production): RedirectResponse
    {
        $this->service->update($production, $request->validated());

        return redirect()->route('contractor-royalty.productions.index')
            ->with('success', __('erp.contractor_royalty.production_updated'));
    }

    public function destroy(ContractorProduction $production): RedirectResponse
    {
        $this->service->delete($production);

        return redirect()->route('contractor-royalty.productions.index')
            ->with('success', __('erp.contractor_royalty.production_deleted'));
    }
}
