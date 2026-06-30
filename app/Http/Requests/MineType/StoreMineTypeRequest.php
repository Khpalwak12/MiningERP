<?php

namespace App\Http\Requests\MineType;

use Illuminate\Foundation\Http\FormRequest;

class StoreMineTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('mine-types.create') ?? false;
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
