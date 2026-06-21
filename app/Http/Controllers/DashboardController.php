<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Resources\CustomerPaymentResource;
use App\Http\Resources\MarbleShipmentResource;
use App\Models\CustomerPayment;
use App\Models\MarbleShipment;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private DashboardService $service)
    {
        $this->middleware('permission:dashboard.view')->only('index');
    }

    public function index(Request $request): Response
    {
        $stats = $this->service->stats();

        return Inertia::render('Dashboard/Index', [
            'stats' => $stats,
            'recentShipments' => MarbleShipmentResource::collection(
                MarbleShipment::with('customer')->latest()->limit(5)->get()
            ),
            'recentPayments' => CustomerPaymentResource::collection(
                CustomerPayment::with('customer')->latest()->limit(5)->get()
            ),
        ]);
    }
}
