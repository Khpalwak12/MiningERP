<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinancialYear\StoreFinancialYearRequest;
use App\Http\Requests\FinancialYear\UpdateFinancialYearRequest;
use App\Http\Resources\FinancialYearResource;
use App\Models\FinancialYear;
use App\Services\FinancialYearService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FinancialYearController extends Controller
{
    public function __construct(private FinancialYearService $service)
    {
        $this->middleware('permission:financial-year.view')->only(['index', 'show']);
        $this->middleware('permission:financial-year.create')->only(['create', 'store']);
        $this->middleware('permission:financial-year.edit')->only(['edit', 'update']);
        $this->middleware('permission:financial-year.close')->only(['close']);
        $this->middleware('permission:financial-year.activate')->only(['activate']);
    }

    public function index(Request $request): Response
    {
        $years = $this->service->paginate($request->only(['search', 'status']));

        return Inertia::render('FinancialYears/Index', [
            'financialYears' => FinancialYearResource::collection($years),
            'filters' => $request->only(['search', 'status']),
            'activeYear' => FinancialYearResource::optional($this->service->active()),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('FinancialYears/Create');
    }

    public function store(StoreFinancialYearRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('financial-years.index')->with('success', __('erp.financial_years.created'));
    }

    public function show(FinancialYear $financialYear): Response
    {
        return Inertia::render('FinancialYears/Show', [
            'financialYear' => new FinancialYearResource($financialYear),
        ]);
    }

    public function edit(FinancialYear $financialYear): Response
    {
        return Inertia::render('FinancialYears/Edit', [
            'financialYear' => new FinancialYearResource($financialYear),
        ]);
    }

    public function update(UpdateFinancialYearRequest $request, FinancialYear $financialYear): RedirectResponse
    {
        $this->service->update($financialYear, $request->validated());

        return redirect()->route('financial-years.index')->with('success', __('erp.financial_years.updated'));
    }

    public function close(FinancialYear $financialYear): RedirectResponse
    {
        $this->service->close($financialYear);

        return redirect()->route('financial-years.index')->with('success', __('erp.financial_years.closed'));
    }

    public function activate(FinancialYear $financialYear): RedirectResponse
    {
        $this->service->activate($financialYear);

        return redirect()->route('financial-years.index')->with('success', __('erp.financial_years.activated'));
    }
}
