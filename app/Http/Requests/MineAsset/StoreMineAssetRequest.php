<?php

namespace App\Http\Requests\MineAsset;

use App\Http\Requests\Concerns\ConvertsShamsiDates;
use App\Http\Requests\Concerns\ValidatesActiveFinancialYear;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMineAssetRequest extends FormRequest
{
    use ConvertsShamsiDates;
    use ValidatesActiveFinancialYear;

    public function authorize(): bool
    {
        return $this->user()?->can('mine-assets.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->convertShamsiDates(['registration_date']);
    }

    public function rules(): array
    {
        return array_merge($this->shamsiDateRules(['registration_date']), [
            'name' => ['required', 'string', 'max:255'],
            'related_to' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'unit' => ['required', 'string', 'max:50'],
            'status' => ['required', Rule::in(['usable', 'unusable'])],
            'remarks' => ['nullable', 'string'],
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $this->ensureWritableFinancialYear($validator);
    }
}
