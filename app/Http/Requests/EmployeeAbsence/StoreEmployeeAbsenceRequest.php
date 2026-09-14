<?php

namespace App\Http\Requests\EmployeeAbsence;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeAbsenceRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('employees.edit') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('absence_date')) {
            $this->convertShamsiDates(['absence_date']);
        }
    }

    public function rules(): array
    {
        return array_merge($this->shamsiDateRules(['absence_date']), [
            'days' => ['required', 'integer', 'min:1', 'max:366'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
