<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\SankariStoneSale\StoreSankariStoneSaleRequest;
use App\Http\Requests\SankariStoneSale\UpdateSankariStoneSaleRequest;
use App\Http\Resources\SankariStoneSaleResource;
use App\Models\SankariStoneSale;
use App\Services\SankariStoneSaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SankariStoneSaleController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private SankariStoneSaleService $service)
    {
        $this->registerModulePermissions('sankari');
    }

    public function index(Request $request): Response
    {
        $sales = $this->service->paginate($request->only(['search', 'date_from', 'date_to', 'payment_type']));

        return Inertia::render('Sankari/Index', [
            'sales' => SankariStoneSaleResource::collection($sales),
            'filters' => $request->only(['search', 'date_from', 'date_to', 'payment_type']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Sankari/Create');
    }

    public function store(StoreSankariStoneSaleRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('sankari.index')->with('success', __('erp.sankari.created'));
    }

    public function show(SankariStoneSale $sankari): Response
    {
        $sankari->load('creator');

        return Inertia::render('Sankari/Show', [
            'sale' => new SankariStoneSaleResource($sankari),
        ]);
    }

    public function edit(SankariStoneSale $sankari): Response
    {
        return Inertia::render('Sankari/Edit', [
            'sale' => new SankariStoneSaleResource($sankari),
        ]);
    }

    public function update(UpdateSankariStoneSaleRequest $request, SankariStoneSale $sankari): RedirectResponse
    {
        $this->service->update($sankari, $request->validated());

        return redirect()->route('sankari.index')->with('success', __('erp.sankari.updated'));
    }

    public function destroy(SankariStoneSale $sankari): RedirectResponse
    {
        $this->service->delete($sankari);

        return redirect()->route('sankari.index')->with('success', __('erp.sankari.deleted'));
    }
}
