import ReportFilterForm from '@/Components/Erp/ReportFilterForm';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { resourceItems } from '@/utils/resource';
import { Head, Link } from '@inertiajs/react';

export default function Payroll({ rows, summaries, filters, employees, selectedEmployee }) {
    const { t } = useTranslation();
    const list = Array.isArray(rows) ? rows : [];
    const summaryList = Array.isArray(summaries) ? summaries : [];

    return (
        <ErpLayout>
            <Head title={t('reports.payroll')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.payroll')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>

            <ReportFilterForm
                reportType="payroll"
                routeName="reports.payroll"
                filters={filters}
                exportType="payroll"
                lookups={{ employees, selectedEmployee }}
            />

            <h2 className="mb-3 text-lg font-semibold">{t('reports.employee_salary_summary')}</h2>
            <div className="mb-8 overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.employee')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.salary')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.months_worked')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.total_earned_salary')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.total_paid_salary')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.remaining_balance')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.overpaid_amount')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.status')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {summaryList.map((row) => (
                            <tr key={row.id}>
                                <td className="px-4 py-3">{row.name}</td>
                                <td className="px-4 py-3">{formatCurrency(row.monthly_salary)}</td>
                                <td className="px-4 py-3">{row.months_worked}</td>
                                <td className="px-4 py-3">{formatCurrency(row.total_earned_salary)}</td>
                                <td className="px-4 py-3">{formatCurrency(row.total_paid_salary)}</td>
                                <td className="px-4 py-3">{formatCurrency(row.remaining_balance)}</td>
                                <td className="px-4 py-3">{formatCurrency(row.overpaid_amount)}</td>
                                <td className="px-4 py-3">{t(`payroll_statuses.${row.payroll_status}`)}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            <h2 className="mb-3 text-lg font-semibold">{t('reports.payment_transactions')}</h2>
            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.employee')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.amount')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.payment_type')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && <tr><td colSpan={4} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>}
                        {list.map((r) => (
                            <tr key={r.id}>
                                <td className="px-4 py-3">{r.payment_date_shamsi || r.payment_date}</td>
                                <td className="px-4 py-3">{r.employee?.name}</td>
                                <td className="px-4 py-3">{formatCurrency(r.amount)}</td>
                                <td className="px-4 py-3">{t(`payment_types.${r.payment_type}`)}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
