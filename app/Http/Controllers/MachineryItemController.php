<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\Machinery\StoreMachineryItemRequest;
use App\Http\Requests\Machinery\UpdateMachineryItemRequest;
use App\Http\Resources\MachineryItemResource;
use App\Models\MachineryItem;
use App\Services\MachineryItemService;
use App\Support\ActiveFinancialYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MachineryItemController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private MachineryItemService $service)
    {
        $this->registerModulePermissions('machinery');
    }

    public function index(Request $request): Response
    {
        $items = $this->service->paginate($request->only(['search', 'date_from', 'date_to', 'currency']));
        $totals = $this->service->totalsByCurrency(ActiveFinancialYear::activeYearId());

        return Inertia::render('Machinery/Index', [
            'items' => MachineryItemResource::collection($items),
            'filters' => $request->only(['search', 'date_from', 'date_to', 'currency']),
            'totals' => $totals,
        ]);
    }

    public function create(): Response|RedirectResponse
    {
        if (ActiveFinancialYear::isAllYearsMode()) {
            return redirect()
                ->route('machinery.index')
                ->with('error', __('erp.financial_years.all_years_read_only'));
        }

        if (! ActiveFinancialYear::activeYear()) {
            return redirect()
                ->route('machinery.index')
                ->with('error', __('erp.financial_years.no_active_year'));
        }

        return Inertia::render('Machinery/Create');
    }

    public function store(StoreMachineryItemRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('machinery.index')->with('success', __('erp.machinery.created'));
    }

    public function edit(MachineryItem $machinery): Response
    {
        return Inertia::render('Machinery/Edit', [
            'item' => new MachineryItemResource($machinery),
        ]);
    }

    public function update(UpdateMachineryItemRequest $request, MachineryItem $machinery): RedirectResponse
    {
        $this->service->update($machinery, $request->validated());

        return redirect()->route('machinery.index')->with('success', __('erp.machinery.updated'));
    }

    public function destroy(MachineryItem $machinery): RedirectResponse
    {
        $this->service->delete($machinery);

        return redirect()->route('machinery.index')->with('success', __('erp.machinery.deleted'));
    }
}
