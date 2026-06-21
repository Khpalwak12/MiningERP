<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\CustomerPayment\StoreCustomerPaymentRequest;
use App\Http\Requests\CustomerPayment\UpdateCustomerPaymentRequest;
use App\Http\Resources\CustomerPaymentResource;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Services\CustomerPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerPaymentController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private CustomerPaymentService $service)
    {
        $this->registerModulePermissions('payments');
    }

    public function index(Request $request): Response
    {
        $payments = $this->service->paginate($request->only(['search', 'date_from', 'date_to', 'customer_id']));

        return Inertia::render('Payments/Index', [
            'payments' => CustomerPaymentResource::collection($payments),
            'filters' => $request->only(['search', 'date_from', 'date_to', 'customer_id']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Payments/Create', [
            'customers' => CustomerResource::collection(Customer::query()->where('status', 'active')->orderBy('name')->get()),
        ]);
    }

    public function store(StoreCustomerPaymentRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('payments.index')->with('success', __('erp.payments.created'));
    }

    public function show(CustomerPayment $payment): Response
    {
        $payment->load(['customer', 'creator']);

        return Inertia::render('Payments/Show', [
            'payment' => new CustomerPaymentResource($payment),
        ]);
    }

    public function edit(CustomerPayment $payment): Response
    {
        $payment->load('customer');

        return Inertia::render('Payments/Edit', [
            'payment' => new CustomerPaymentResource($payment),
            'customers' => CustomerResource::collection(Customer::query()->where('status', 'active')->orderBy('name')->get()),
        ]);
    }

    public function update(UpdateCustomerPaymentRequest $request, CustomerPayment $payment): RedirectResponse
    {
        $this->service->update($payment, $request->validated());

        return redirect()->route('payments.index')->with('success', __('erp.payments.updated'));
    }

    public function destroy(CustomerPayment $payment): RedirectResponse
    {
        $this->service->delete($payment);

        return redirect()->route('payments.index')->with('success', __('erp.payments.deleted'));
    }
}
