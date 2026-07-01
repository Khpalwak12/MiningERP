<?php

namespace App\Http\Requests\Backup;

use Illuminate\Foundation\Http\FormRequest;

class RestoreBackupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('backup.restore') ?? false;
    }

    public function rules(): array
    {
        return [
            'filename' => ['nullable', 'string', 'required_without:backup_file'],
            'backup_file' => ['nullable', 'file', 'mimes:zip', 'max:512000', 'required_without:filename'],
        ];
    }
}
