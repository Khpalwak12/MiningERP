<?php

namespace App\Http\Requests\MineAsset;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMineAssetRequest extends FormRequest
{
    use ConvertsShamsiDates;

    public function authorize(): bool
    {
        return $this->user()?->can('mine-assets.edit') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('registration_date')) {
            $this->convertShamsiDates(['registration_date']);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'related_to' => ['nullable', 'string', 'max:255'],
            'quantity' => ['sometimes', 'required', 'numeric', 'min:0.001'],
            'unit' => ['sometimes', 'required', 'string', 'max:50'],
            'status' => ['sometimes', 'required', Rule::in(['usable', 'unusable'])],
            'registration_date' => ['sometimes', 'required', 'date'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
