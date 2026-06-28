<?php

namespace App\Http\Requests\ContractorProduction;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContractorProductionRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('contractor-royalty.edit') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->convertShamsiDates(['production_date']);

        if ($this->has('truck_number') && $this->input('truck_number') === '') {
            $this->merge(['truck_number' => null]);
        }

        if ($this->has('remarks') && $this->input('remarks') === '') {
            $this->merge(['remarks' => null]);
        }
    }

    public function rules(): array
    {
        return array_merge($this->shamsiDateRules(['production_date']), [
            'truck_number' => ['nullable', 'string', 'max:255'],
            'quantity_ton' => ['sometimes', 'required', 'numeric', 'min:0.001'],
            'rate_per_ton' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'remarks' => ['nullable', 'string'],
        ]);
    }
}
