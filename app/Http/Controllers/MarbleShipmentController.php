<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\MarbleShipment\StoreMarbleShipmentRequest;
use App\Http\Requests\MarbleShipment\UpdateMarbleShipmentRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\MarbleShipmentResource;
use App\Models\Customer;
use App\Models\MarbleShipment;
use App\Services\MarbleShipmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarbleShipmentController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private MarbleShipmentService $service)
    {
        $this->registerModulePermissions('shipments');
    }

    public function index(Request $request): Response
    {
        $shipments = $this->service->paginate($request->only(['search', 'date_from', 'date_to', 'customer_id', 'status']));

        return Inertia::render('Shipments/Index', [
            'shipments' => MarbleShipmentResource::collection($shipments),
            'filters' => $request->only(['search', 'date_from', 'date_to', 'customer_id', 'status']),
            'customers' => CustomerResource::collection(Customer::query()->where('status', 'active')->orderBy('name')->get()),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Shipments/Create', [
            'customers' => CustomerResource::collection(Customer::query()->where('status', 'active')->orderBy('name')->get()),
        ]);
    }

    public function store(StoreMarbleShipmentRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('shipments.index')->with('success', __('erp.shipments.created'));
    }

    public function show(MarbleShipment $shipment): Response
    {
        $shipment->load(['customer', 'creator']);

        return Inertia::render('Shipments/Show', [
            'shipment' => new MarbleShipmentResource($shipment),
        ]);
    }

    public function edit(MarbleShipment $shipment): Response
    {
        $shipment->load(['customer']);

        return Inertia::render('Shipments/Edit', [
            'shipment' => new MarbleShipmentResource($shipment),
            'customers' => CustomerResource::collection(Customer::query()->where('status', 'active')->orderBy('name')->get()),
        ]);
    }

    public function update(UpdateMarbleShipmentRequest $request, MarbleShipment $shipment): RedirectResponse
    {
        $this->service->update($shipment, $request->validated());

        return redirect()->route('shipments.index')->with('success', __('erp.shipments.updated'));
    }

    public function destroy(MarbleShipment $shipment): RedirectResponse
    {
        $this->service->delete($shipment);

        return redirect()->route('shipments.index')->with('success', __('erp.shipments.deleted'));
    }
}
