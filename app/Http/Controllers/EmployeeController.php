<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RegistersModulePermissions;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    use RegistersModulePermissions;

    public function __construct(private EmployeeService $service)
    {
        $this->registerModulePermissions('employees');
    }

    public function index(Request $request): Response
    {
        $employees = $this->service->paginate($request->only(['search', 'date_from', 'date_to', 'status']));

        return Inertia::render('Employees/Index', [
            'employees' => EmployeeResource::collection($employees),
            'filters' => $request->only(['search', 'date_from', 'date_to', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Employees/Create');
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('employees.index')->with('success', __('erp.employees.created'));
    }

    public function show(Employee $employee): Response
    {
        $employee->load('payrollPayments');
        $employee->loadSum('payrollPayments as total_paid_sum', 'amount');

        return Inertia::render('Employees/Show', [
            'employee' => new EmployeeResource($employee),
        ]);
    }

    public function edit(Employee $employee): Response
    {
        return Inertia::render('Employees/Edit', [
            'employee' => new EmployeeResource($employee),
        ]);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $this->service->update($employee, $request->validated());

        return redirect()->route('employees.index')->with('success', __('erp.employees.updated'));
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $this->service->delete($employee);

        return redirect()->route('employees.index')->with('success', __('erp.employees.deleted'));
    }
}
