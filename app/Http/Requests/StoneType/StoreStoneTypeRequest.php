<?php

namespace App\Http\Requests\StoneType;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoneTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('stone-types.create') ?? false;
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
