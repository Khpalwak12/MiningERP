<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAbsence extends Model
{
    protected $fillable = [
        'employee_id',
        'absence_date',
        'days',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'absence_date' => 'date',
            'days' => 'integer',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
