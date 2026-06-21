<?php

namespace App\Http\Requests\Concerns;

use App\Support\JalaliDate;

trait ConvertsShamsiDates
{
    protected function convertShamsiDates(array $fields): void
    {
        $merged = [];

        foreach ($fields as $field) {
            if ($this->filled($field)) {
                $merged[$field] = JalaliDate::toGregorian($this->input($field))->format('Y-m-d');
            }
        }

        if ($merged !== []) {
            $this->merge($merged);
        }
    }

    protected function shamsiDateRules(array $fields, bool $required = true): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $rules[$field] = array_filter([
                $required ? 'required' : 'nullable',
                'date',
            ]);
        }

        return $rules;
    }
}
