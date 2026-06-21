<?php

namespace App\Http\Requests\CustomerPayment;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerPaymentRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('payments.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->convertShamsiDates(['payment_date']);

        if ($this->has('receipt_number') && $this->input('receipt_number') === '') {
            $this->merge(['receipt_number' => null]);
        }
    }

    public function rules(): array
    {
        return array_merge($this->shamsiDateRules(['payment_date']), [
            'customer_id' => ['required', 'exists:customers,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'receipt_number' => ['nullable', 'string', 'max:255', 'unique:customer_payments,receipt_number'],
            'received_by' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
