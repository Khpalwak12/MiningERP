<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\MineType;
use App\Models\ExpenseCategory;
use App\Models\PersonalContact;

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

        if (! empty($filters['status'])) {
            $statusKey = 'erp.shipments.statuses.'.$filters['status'];
            $summary[__('erp.fields.status')] = __($statusKey) !== $statusKey
                ? __($statusKey)
                : $filters['status'];
        }

        if (! empty($filters['personal_contact_id'])) {
            $contact = PersonalContact::query()->find($filters['personal_contact_id']);
            if ($contact) {
                $summary[__('erp.fields.name')] = $contact->name;
            }
        }

        if (! empty($filters['currency'])) {
            $currencyKey = 'erp.currencies.'.$filters['currency'];
            $summary[__('erp.fields.currency')] = __($currencyKey) !== $currencyKey
                ? __($currencyKey)
                : $filters['currency'];
        }

        if (! empty($filters['filter_by']) && ($filters['filter_value'] ?? '') !== '') {
            $labelKey = 'erp.report_filters.'.$filters['filter_by'];
            $fieldLabel = __($labelKey) !== $labelKey
                ? __($labelKey)
                : (__('erp.fields.'.$filters['filter_by']) !== 'erp.fields.'.$filters['filter_by']
                    ? __('erp.fields.'.$filters['filter_by'])
                    : $filters['filter_by']);

            $summary[$fieldLabel] = self::resolveFilterValue($filters['filter_by'], $filters['filter_value']);
        }

        return $summary;
    }

    private static function resolveFilterValue(string $filterBy, mixed $value): string
    {
        return match ($filterBy) {
            'customer' => Customer::query()->find($value)?->name ?? (string) $value,
            'employee' => Employee::query()->find($value)?->name ?? (string) $value,
            'category' => ExpenseCategory::query()->find($value)?->localized_name ?? (string) $value,
            'mine_type' => MineType::query()->find($value)?->localized_name ?? (string) $value,
            'status', 'employee_status' => __('erp.status.'.$value) !== 'erp.status.'.$value
                ? __('erp.status.'.$value)
                : (string) $value,
            'movement_type' => __('erp.movement_types.'.$value) !== 'erp.movement_types.'.$value
                ? __('erp.movement_types.'.$value)
                : (string) $value,
            'payment_type' => __('erp.payment_types.'.$value) !== 'erp.payment_types.'.$value
                ? __('erp.payment_types.'.$value)
                : (string) $value,
            'transaction_type' => __('erp.personal_transaction_types.'.$value) !== 'erp.personal_transaction_types.'.$value
                ? __('erp.personal_transaction_types.'.$value)
                : (string) $value,
            default => (string) $value,
        };
    }
}
