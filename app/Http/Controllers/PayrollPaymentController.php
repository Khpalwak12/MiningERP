<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\PayrollPayment\StorePayrollPaymentRequest;
use App\Http\Requests\PayrollPayment\UpdatePayrollPaymentRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\PayrollPaymentResource;
use App\Models\Employee;
use App\Models\PayrollPayment;
use App\Services\PayrollPaymentService;
use App\Support\ActiveFinancialYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PayrollPaymentController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private PayrollPaymentService $service)
    {
        $this->registerModulePermissions('payroll');
    }

    public function index(Request $request): Response
    {
        $payments = $this->service->paginate($request->only(['search', 'date_from', 'date_to', 'employee_id', 'payment_type']));

        return Inertia::render('Payroll/Index', [
            'payments' => PayrollPaymentResource::collection($payments),
            'filters' => $request->only(['search', 'date_from', 'date_to', 'employee_id', 'payment_type']),
            'employees' => EmployeeResource::collection(
                Employee::query()->withPayrollTotal()->where('status', 'active')->orderBy('name')->get()
            ),
        ]);
    }

    public function create(): Response|RedirectResponse
    {
        if (ActiveFinancialYear::isAllYearsMode()) {
            return redirect()
                ->route('payroll.index')
                ->with('error', __('erp.financial_years.all_years_read_only'));
        }

        if (! ActiveFinancialYear::activeYear()) {
            return redirect()
                ->route('payroll.index')
                ->with('error', __('erp.financial_years.no_active_year'));
        }

        return Inertia::render('Payroll/Create', [
            'employees' => EmployeeResource::collection(
                Employee::query()->withPayrollTotal()->where('status', 'active')->orderBy('name')->get()
            ),
        ]);
    }

    public function store(StorePayrollPaymentRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('payroll.index')->with('success', __('erp.payroll.created'));
    }

    public function show(PayrollPayment $payroll): Response
    {
        $payroll->load(['employee', 'creator']);

        return Inertia::render('Payroll/Show', [
            'payment' => new PayrollPaymentResource($payroll),
        ]);
    }

    public function edit(PayrollPayment $payroll): Response
    {
        $payroll->load('employee');

        return Inertia::render('Payroll/Edit', [
            'payment' => new PayrollPaymentResource($payroll),
            'employees' => EmployeeResource::collection(
                Employee::query()->withPayrollTotal()->where('status', 'active')->orderBy('name')->get()
            ),
        ]);
    }

    public function update(UpdatePayrollPaymentRequest $request, PayrollPayment $payroll): RedirectResponse
    {
        $this->service->update($payroll, $request->validated());

        return redirect()->route('payroll.index')->with('success', __('erp.payroll.updated'));
    }

    public function destroy(PayrollPayment $payroll): RedirectResponse
    {
        $this->service->delete($payroll);

        return redirect()->route('payroll.index')->with('success', __('erp.payroll.deleted'));
    }
}
