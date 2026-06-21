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
    }

    public function rules(): array
    {
        return array_merge($this->shamsiDateRules(['joining_date'], false), [
            'name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'position' => ['nullable', 'string', 'max:255'],
            'salary' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
    }
}
