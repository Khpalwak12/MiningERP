<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeAbsence\StoreEmployeeAbsenceRequest;
use App\Models\Employee;
use App\Models\EmployeeAbsence;
use Illuminate\Http\RedirectResponse;

class EmployeeAbsenceController extends Controller
{
    public function store(StoreEmployeeAbsenceRequest $request, Employee $employee): RedirectResponse
    {
        $employee->absences()->create($request->validated());

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', __('erp.employee_absences.created'));
    }

    public function destroy(Employee $employee, EmployeeAbsence $absence): RedirectResponse
    {
        abort_unless(request()->user()?->can('employees.edit'), 403);
        abort_unless($absence->employee_id === $employee->id, 404);

        $absence->delete();

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', __('erp.employee_absences.deleted'));
    }
}
