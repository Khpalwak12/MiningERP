<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\ExpenseCategory;

class ReportFilterSummary
{
    public static function forExport(array $filters): array
    {
        $summary = [];

        if (! empty($filters['date_from'])) {
            $summary[__('erp.fields.date_from')] = $filters['date_from'];
        }

        if (! empty($filters['date_to'])) {
            $summary[__('erp.fields.date_to')] = $filters['date_to'];
        }

        if (! empty($filters['customer_id'])) {
            $customer = Customer::query()->find($filters['customer_id']);
            if ($customer) {
                $summary[__('erp.fields.customer')] = $customer->name;
            }
        }

        if (! empty($filters['receipt_number'])) {
            $summary[__('erp.fields.receipt_number')] = $filters['receipt_number'];
        }

        if (! empty($filters['employee_id'])) {
            $employee = Employee::query()->find($filters['employee_id']);
            if ($employee) {
                $summary[__('erp.fields.employee')] = $employee->name;
            }
        }

        if (! empty($filters['expense_category_id'])) {
            $category = ExpenseCategory::query()->find($filters['expense_category_id']);
            if ($category) {
                $summary[__('erp.fields.category')] = $category->localized_name;
            }
        }

        if (! empty($filters['status'])) {
            $statusKey = 'erp.shipments.statuses.'.$filters['status'];
            $summary[__('erp.fields.status')] = __($statusKey) !== $statusKey
                ? __($statusKey)
                : $filters['status'];
        }

        if (! empty($filters['low_stock']) && filter_var($filters['low_stock'], FILTER_VALIDATE_BOOLEAN)) {
            $summary[__('erp.reports.low_stock_only')] = '✓';
        }

        return $summary;
    }
}
