<?php

namespace App\Http\Controllers;

use App\Services\ContractorRoyaltyLedgerService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContractorRoyaltyLedgerController extends Controller
{
    public function __construct(private ContractorRoyaltyLedgerService $ledgerService)
    {
        $this->middleware('permission:contractor-royalty.view')->only('index');
    }

    public function index(Request $request): Response
    {
        $filters = $request->only(['date_from', 'date_to']);

        return Inertia::render('ContractorRoyalty/Ledger/Index', [
            'summary' => $this->ledgerService->summary($filters),
            'transactions' => $this->ledgerService->transactions($filters),
            'filters' => $filters,
        ]);
    }
}
