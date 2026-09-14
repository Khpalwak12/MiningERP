<?php

namespace App\Http\Requests\Employee;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('employees.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('joining_date')) {
            $this->convertShamsiDates(['joining_date']);
        }

        if ($this->filled('end_date')) {
            $this->convertShamsiDates(['end_date']);
        } else {
            $this->merge(['end_date' => null]);
        }

        $this->normalizeSharedContractorFields();
    }

    public function rules(): array
    {
        return array_merge($this->shamsiDateRules(['joining_date', 'end_date'], false), [
            'name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'position' => ['nullable', 'string', 'max:255'],
            'salary' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'is_shared_with_contractor' => ['sometimes', 'boolean'],
            'contractor_salary_share_percent' => ['nullable', 'required_if:is_shared_with_contractor,1,true', 'numeric', 'min:0.01', 'max:100'],
        ]);
    }

    private function normalizeSharedContractorFields(): void
    {
        $isShared = filter_var($this->input('is_shared_with_contractor', false), FILTER_VALIDATE_BOOLEAN);

        $this->merge([
            'is_shared_with_contractor' => $isShared,
            'contractor_salary_share_percent' => $isShared
                ? ($this->input('contractor_salary_share_percent') ?: 50)
                : null,
        ]);
    }
}
