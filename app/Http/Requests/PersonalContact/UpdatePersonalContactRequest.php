<?php

namespace App\Http\Requests\PersonalContact;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePersonalContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('personal-accounts.edit') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'contact_type' => ['sometimes', 'required', Rule::in(['person', 'shopkeeper'])],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', Rule::in(['active', 'inactive'])],
        ];
    }
}
