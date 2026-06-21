<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private CustomerService $service)
    {
        $this->registerModulePermissions('customers');
    }

    public function index(Request $request): Response
    {
        $customers = $this->service->paginate($request->only(['search', 'date_from', 'date_to', 'status']));

        return Inertia::render('Customers/Index', [
            'customers' => CustomerResource::collection($customers),
            'filters' => $request->only(['search', 'date_from', 'date_to', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Customers/Create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('customers.index')->with('success', __('erp.customers.created'));
    }

    public function show(Customer $customer): Response
    {
        $customer->load(['shipments', 'payments']);
        $customer->loadSum('shipments as total_sales_sum', 'total_amount');
        $customer->loadSum('payments as total_payments_sum', 'amount');

        return Inertia::render('Customers/Show', [
            'customer' => new CustomerResource($customer),
            'ledger' => $this->service->ledger($customer),
        ]);
    }

    public function edit(Customer $customer): Response
    {
        return Inertia::render('Customers/Edit', [
            'customer' => new CustomerResource($customer),
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->service->update($customer, $request->validated());

        return redirect()->route('customers.index')->with('success', __('erp.customers.updated'));
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $this->service->delete($customer);

        return redirect()->route('customers.index')->with('success', __('erp.customers.deleted'));
    }
}
