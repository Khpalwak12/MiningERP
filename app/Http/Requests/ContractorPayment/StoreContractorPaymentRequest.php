<?php

namespace App\Http\Requests\ContractorPayment;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;

class StoreContractorPaymentRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('contractor-royalty.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->convertShamsiDates(['payment_date']);

        if ($this->has('receipt_number') && $this->input('receipt_number') === '') {
            $this->merge(['receipt_number' => null]);
        }

        if ($this->has('remarks') && $this->input('remarks') === '') {
            $this->merge(['remarks' => null]);
        }
    }

    public function rules(): array
    {
        return array_merge($this->shamsiDateRules(['payment_date']), [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'receipt_number' => ['nullable', 'string', 'max:255'],
            'received_by' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ]);
    }
}
