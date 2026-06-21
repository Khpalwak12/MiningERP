<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\Inventory\StoreInventoryItemRequest;
use App\Http\Requests\Inventory\UpdateInventoryItemRequest;
use App\Http\Resources\InventoryItemResource;
use App\Models\InventoryItem;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InventoryItemController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private InventoryService $service)
    {
        $this->registerModulePermissions('inventory');
    }

    public function index(Request $request): Response
    {
        $items = $this->service->paginateItems($request->only(['search', 'date_from', 'date_to', 'low_stock']));

        return Inertia::render('Inventory/Index', [
            'items' => InventoryItemResource::collection($items),
            'filters' => $request->only(['search', 'date_from', 'date_to', 'low_stock']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Inventory/Create');
    }

    public function store(StoreInventoryItemRequest $request): RedirectResponse
    {
        $this->service->createItem($request->validated());

        return redirect()->route('inventory.index')->with('success', __('erp.inventory.created'));
    }

    public function show(InventoryItem $inventoryItem): Response
    {
        $inventoryItem->load('movements.creator');

        return Inertia::render('Inventory/Show', [
            'item' => new InventoryItemResource($inventoryItem),
        ]);
    }

    public function edit(InventoryItem $inventoryItem): Response
    {
        return Inertia::render('Inventory/Edit', [
            'item' => new InventoryItemResource($inventoryItem),
        ]);
    }

    public function update(UpdateInventoryItemRequest $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $this->service->updateItem($inventoryItem, $request->validated());

        return redirect()->route('inventory.index')->with('success', __('erp.inventory.updated'));
    }

    public function destroy(InventoryItem $inventoryItem): RedirectResponse
    {
        $this->service->deleteItem($inventoryItem);

        return redirect()->route('inventory.index')->with('success', __('erp.inventory.deleted'));
    }
}
