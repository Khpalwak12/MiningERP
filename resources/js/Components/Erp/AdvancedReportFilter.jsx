import CustomerSelect from '@/Components/Erp/CustomerSelect';
import EmployeeSelect from '@/Components/Erp/EmployeeSelect';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import TextInput from '@/Components/TextInput';
import useTranslation from '@/hooks/useTranslation';
import { resourceData, resourceItems } from '@/utils/resource';
import {
    EMPLOYEE_STATUS_OPTIONS,
    getFilterOption,
    getFilterOptions,
    MOVEMENT_TYPES,
    PAYROLL_PAYMENT_TYPES,
} from '@/config/reportFilterConfig';
import { PERSONAL_CURRENCIES, PERSONAL_TRANSACTION_TYPES } from '@/config/personalAccountConfig';

function FilterValueInput({
    reportType,
    filterBy,
    value,
    onChange,
    lookups = {},
}) {
    const { t } = useTranslation();
    const option = getFilterOption(reportType, filterBy);

    if (!filterBy || !option) {
        return (
            <TextInput
                className="mt-0 block w-full min-w-[180px]"
                value=""
                disabled
                placeholder="—"
            />
        );
    }

    switch (option.type) {
        case 'customer':
            return (
                <CustomerSelect
                    customers={lookups.customers}
                    value={value}
                    onChange={onChange}
                    selectedCustomer={resourceData(lookups.selectedCustomer)}
                />
            );
        case 'employee':
            return (
                <EmployeeSelect
                    employees={lookups.employees}
                    value={value}
                    onChange={onChange}
                    selectedEmployee={resourceData(lookups.selectedEmployee)}
                />
            );
        case 'category':
            return (
                <select
                    className="mt-0 block w-full min-w-[180px] rounded-md border-gray-300 text-sm"
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                >
                    <option value="">{t('actions.filter')} — {t('fields.category')}</option>
                    {resourceItems(lookups.expenseCategories).map((category) => (
                        <option key={category.id} value={category.id}>{category.name}</option>
                    ))}
                </select>
            );
        case 'employee_status':
            return (
                <select
                    className="mt-0 block w-full min-w-[180px] rounded-md border-gray-300 text-sm"
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                >
                    <option value="">{t('actions.filter')} — {t('fields.status')}</option>
                    {EMPLOYEE_STATUS_OPTIONS.map((status) => (
                        <option key={status} value={status}>{t(`status.${status}`)}</option>
                    ))}
                </select>
            );
        case 'payment_type':
            return (
                <select
                    className="mt-0 block w-full min-w-[180px] rounded-md border-gray-300 text-sm"
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                >
                    <option value="">{t('actions.filter')} — {t('fields.payment_type')}</option>
                    {PAYROLL_PAYMENT_TYPES.map((type) => (
                        <option key={type} value={type}>{t(`payment_types.${type}`)}</option>
                    ))}
                </select>
            );
        case 'movement_type':
            return (
                <select
                    className="mt-0 block w-full min-w-[180px] rounded-md border-gray-300 text-sm"
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                >
                    <option value="">{t('actions.filter')} — {t('fields.movement_type')}</option>
                    {MOVEMENT_TYPES.map((type) => (
                        <option key={type} value={type}>{t(`movement_types.${type}`)}</option>
                    ))}
                </select>
            );
        case 'personal_transaction_type':
            return (
                <select
                    className="mt-0 block w-full min-w-[180px] rounded-md border-gray-300 text-sm"
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                >
                    <option value="">{t('actions.filter')} — {t('fields.type')}</option>
                    {PERSONAL_TRANSACTION_TYPES.map((type) => (
                        <option key={type} value={type}>{t(`personal_transaction_types.${type}`)}</option>
                    ))}
                </select>
            );
        case 'currency':
            return (
                <select
                    className="mt-0 block w-full min-w-[180px] rounded-md border-gray-300 text-sm"
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                >
                    <option value="">{t('actions.filter')} — {t('fields.currency')}</option>
                    {PERSONAL_CURRENCIES.map((currency) => (
                        <option key={currency} value={currency}>{t(`currencies.${currency}`)}</option>
                    ))}
                </select>
            );
        case 'number':
            return (
                <TextInput
                    type="number"
                    step="any"
                    className="mt-0 block w-full min-w-[180px]"
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                />
            );
        case 'date':
            return <ShamsiDateInput value={value} onChange={onChange} />;
        case 'text':
        default:
            return (
                <TextInput
                    className="mt-0 block w-full min-w-[180px]"
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                    placeholder={t('actions.search')}
                />
            );
    }
}

export default function AdvancedReportFilter({
    reportType,
    filterBy,
    filterValue,
    onFilterByChange,
    onFilterValueChange,
    lookups = {},
}) {
    const { t } = useTranslation();
    const options = getFilterOptions(reportType);

    if (options.length === 0) {
        return null;
    }

    return (
        <div className="flex w-full flex-wrap items-end gap-4 border-t border-gray-100 pt-4">
            <div>
                <label className="mb-1 block text-sm font-medium text-gray-700">{t('report_filters.filter_by')}</label>
                <select
                    className="mt-0 block min-w-[180px] rounded-md border-gray-300 text-sm"
                    value={filterBy}
                    onChange={(e) => onFilterByChange(e.target.value)}
                >
                    <option value="">{t('report_filters.filter_by')} —</option>
                    {options.map((option) => (
                        <option key={option.value} value={option.value}>{t(option.labelKey)}</option>
                    ))}
                </select>
            </div>
            <div className="min-w-[220px]">
                <label className="mb-1 block text-sm font-medium text-gray-700">{t('report_filters.value')}</label>
                <FilterValueInput
                    reportType={reportType}
                    filterBy={filterBy}
                    value={filterValue}
                    onChange={onFilterValueChange}
                    lookups={lookups}
                />
            </div>
        </div>
    );
}
