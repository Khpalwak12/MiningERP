<?php

namespace App\Http\Requests\MineType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMineTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('mine-types.edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'name_en' => ['required', 'string', 'max:255'],
            'name_ps' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
