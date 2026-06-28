<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\ContractorPayment\StoreContractorPaymentRequest;
use App\Http\Requests\ContractorPayment\UpdateContractorPaymentRequest;
use App\Http\Resources\ContractorPaymentResource;
use App\Models\ContractorPayment;
use App\Services\ContractorPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContractorPaymentController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private ContractorPaymentService $service)
    {
        $this->registerModulePermissions('contractor-royalty');
    }

    public function index(Request $request): Response
    {
        $payments = $this->service->paginate($request->only(['search', 'date_from', 'date_to']));

        return Inertia::render('ContractorRoyalty/Payments/Index', [
            'payments' => ContractorPaymentResource::collection($payments),
            'filters' => $request->only(['search', 'date_from', 'date_to']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('ContractorRoyalty/Payments/Create');
    }

    public function store(StoreContractorPaymentRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('contractor-royalty.payments.index')
            ->with('success', __('erp.contractor_royalty.payment_created'));
    }

    public function show(ContractorPayment $payment): Response
    {
        $payment->load('creator');

        return Inertia::render('ContractorRoyalty/Payments/Show', [
            'payment' => new ContractorPaymentResource($payment),
        ]);
    }

    public function edit(ContractorPayment $payment): Response
    {
        return Inertia::render('ContractorRoyalty/Payments/Edit', [
            'payment' => new ContractorPaymentResource($payment),
        ]);
    }

    public function update(UpdateContractorPaymentRequest $request, ContractorPayment $payment): RedirectResponse
    {
        $this->service->update($payment, $request->validated());

        return redirect()->route('contractor-royalty.payments.index')
            ->with('success', __('erp.contractor_royalty.payment_updated'));
    }

    public function destroy(ContractorPayment $payment): RedirectResponse
    {
        $this->service->delete($payment);

        return redirect()->route('contractor-royalty.payments.index')
            ->with('success', __('erp.contractor_royalty.payment_deleted'));
    }
}
