<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('inventory.edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('inventory_items', 'sku')->ignore($this->route('inventory_item'))],
            'unit' => ['sometimes', 'required', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:100'],
            'min_stock' => ['sometimes', 'required', 'numeric', 'min:0'],
            'current_stock' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
