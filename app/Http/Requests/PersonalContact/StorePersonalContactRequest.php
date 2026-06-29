<?php

namespace App\Http\Requests\PersonalContact;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePersonalContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('personal-accounts.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'contact_type' => ['required', Rule::in(['person', 'shopkeeper'])],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
