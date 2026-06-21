<?php

namespace App\Http\Requests\CustomerPayment;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerPaymentRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('payments.edit') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('payment_date')) {
            $this->convertShamsiDates(['payment_date']);
        }

        if ($this->has('receipt_number') && $this->input('receipt_number') === '') {
            $this->merge(['receipt_number' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['sometimes', 'required', 'exists:customers,id'],
            'payment_date' => ['sometimes', 'required', 'date'],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'receipt_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('customer_payments', 'receipt_number')->ignore($this->route('payment')),
            ],
            'received_by' => ['sometimes', 'required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
