<?php

namespace App\Http\Requests\Inventory;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryMovementRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('inventory.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->convertShamsiDates(['movement_date']);
    }

    public function rules(): array
    {
        return array_merge($this->shamsiDateRules(['movement_date']), [
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'movement_type' => ['required', Rule::in(['in', 'out'])],
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
