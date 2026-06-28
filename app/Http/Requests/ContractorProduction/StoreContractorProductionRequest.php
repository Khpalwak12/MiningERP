<?php

namespace App\Http\Requests\ContractorProduction;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;

class StoreContractorProductionRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('contractor-royalty.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->convertShamsiDates(['production_date']);

        if ($this->has('remarks') && $this->input('remarks') === '') {
            $this->merge(['remarks' => null]);
        }

        if (! $this->filled('rate_per_ton')) {
            $this->merge([
                'rate_per_ton' => config('erp.contractor_royalty.default_rate_per_ton', 150),
            ]);
        }
    }

    public function rules(): array
    {
        return array_merge($this->shamsiDateRules(['production_date']), [
            'quantity_ton' => ['required', 'numeric', 'min:0.001'],
            'rate_per_ton' => ['required', 'numeric', 'min:0.01'],
            'remarks' => ['nullable', 'string'],
        ]);
    }
}
