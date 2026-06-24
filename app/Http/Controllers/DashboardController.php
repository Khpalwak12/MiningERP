<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Resources\CustomerPaymentResource;
use App\Http\Resources\MarbleShipmentResource;
use App\Models\CustomerPayment;
use App\Models\FinancialYear;
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
        $yearId = FinancialYear::query()->active()->value('id');

        return Inertia::render('Dashboard/Index', [
            'stats' => $stats,
            'recentShipments' => MarbleShipmentResource::collection(
                MarbleShipment::with('customer')
                    ->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId))
                    ->latest()
                    ->limit(5)
                    ->get()
            ),
            'recentPayments' => CustomerPaymentResource::collection(
                CustomerPayment::with('customer')
                    ->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId))
                    ->latest()
                    ->limit(5)
                    ->get()
            ),
        ]);
    }
}
