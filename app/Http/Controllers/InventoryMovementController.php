<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inventory\StoreInventoryMovementRequest;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\InventoryMovementResource;
use App\Models\InventoryItem;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InventoryMovementController extends Controller
{
    public function __construct(private InventoryService $service)
    {
        $this->middleware('permission:inventory.view')->only('index');
        $this->middleware('permission:inventory.create')->only(['create', 'store']);
    }

    public function index(Request $request, InventoryItem $inventoryItem): Response
    {
        $movements = $this->service->paginateMovements(
            $inventoryItem->id,
            $request->only(['search', 'date_from', 'date_to', 'movement_type'])
        );

        return Inertia::render('Inventory/Movements/Index', [
            'item' => new InventoryItemResource($inventoryItem),
            'movements' => InventoryMovementResource::collection($movements),
            'filters' => $request->only(['search', 'date_from', 'date_to', 'movement_type']),
        ]);
    }

    public function create(InventoryItem $inventoryItem): Response
    {
        return Inertia::render('Inventory/Movements/Create', [
            'item' => new InventoryItemResource($inventoryItem),
        ]);
    }

    public function store(StoreInventoryMovementRequest $request): RedirectResponse
    {
        $movement = $this->service->recordMovement($request->validated());

        return redirect()
            ->route('inventory.movements.index', $movement->inventory_item_id)
            ->with('success', __('erp.inventory.movement_created'));
    }
}
