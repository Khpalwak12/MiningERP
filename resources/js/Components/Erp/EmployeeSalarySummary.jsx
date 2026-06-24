import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';

const statusStyles = {
    credit: 'text-amber-700',
    settled: 'text-green-700',
    overpaid: 'text-red-700',
};

export default function EmployeeSalarySummary({ employee, compact = false }) {
    const { t } = useTranslation();

    if (!employee?.id) {
        return null;
    }

    const status = employee.payroll_status || 'settled';
    const statusClass = statusStyles[status] || 'text-gray-700';

    const items = [
        { label: t('fields.salary'), value: formatCurrency(employee.monthly_salary ?? employee.salary) },
        { label: t('fields.joining_date'), value: employee.joining_date_shamsi || '—' },
        { label: t('fields.current_shamsi_date'), value: employee.current_shamsi_date || '—' },
        { label: t('fields.months_worked'), value: employee.months_worked ?? 0 },
        { label: t('fields.total_earned_salary'), value: formatCurrency(employee.total_earned_salary ?? 0) },
        { label: t('fields.total_paid_salary'), value: formatCurrency(employee.total_paid_salary ?? employee.total_paid ?? 0) },
        { label: t('fields.remaining_balance'), value: formatCurrency(employee.remaining_balance ?? employee.remaining_salary ?? 0), highlight: status === 'credit' },
        { label: t('fields.overpaid_amount'), value: formatCurrency(employee.overpaid_amount ?? 0), highlight: status === 'overpaid' },
    ];

    if (compact) {
        const displayItems = [
            { label: t('fields.employee'), value: employee.name },
            { label: t('fields.salary'), value: formatCurrency(employee.monthly_salary ?? employee.salary) },
            { label: t('fields.months_worked'), value: employee.months_worked ?? 0 },
            { label: t('fields.total_earned_salary'), value: formatCurrency(employee.total_earned_salary ?? 0) },
            { label: t('fields.total_paid_salary'), value: formatCurrency(employee.total_paid_salary ?? employee.total_paid ?? 0) },
            { label: t('fields.remaining_balance'), value: formatCurrency(employee.remaining_balance ?? 0) },
            { label: t('fields.overpaid_amount'), value: formatCurrency(employee.overpaid_amount ?? 0) },
        ];

        return (
            <div className="rounded-lg border border-indigo-100 bg-indigo-50 p-4">
                <h2 className="mb-3 text-sm font-semibold text-indigo-900">{t('payroll.employee_summary')}</h2>
                <dl className="grid gap-2 text-sm sm:grid-cols-2">
                    {displayItems.map((item) => (
                        <div key={item.label}>
                            <dt className="text-gray-600">{item.label}</dt>
                            <dd className="font-medium">{item.value}</dd>
                        </div>
                    ))}
                    <div className="sm:col-span-2">
                        <dt className="text-gray-600">{t('fields.status')}</dt>
                        <dd className={`font-semibold ${statusClass}`}>{t(`payroll_statuses.${status}`)}</dd>
                    </div>
                </dl>
            </div>
        );
    }

    return (
        <div className="space-y-4">
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {items.map((item) => (
                    <div key={item.label} className="rounded-lg bg-white p-4 shadow">
                        <p className="text-sm text-gray-500">{item.label}</p>
                        <p className={`text-xl font-bold ${item.highlight ? statusClass : ''}`}>{item.value}</p>
                    </div>
                ))}
            </div>
            <div className="rounded-lg bg-white p-4 shadow">
                <p className="text-sm text-gray-500">{t('fields.status')}</p>
                <p className={`text-lg font-semibold ${statusClass}`}>{t(`payroll_statuses.${status}`)}</p>
            </div>
        </div>
    );
}
