import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ReportExportButtons from '@/Components/Erp/ReportExportButtons';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { buildExportQuery } from '@/utils/reportExport';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Payroll({ rows, summaries, filters }) {
    const { t } = useTranslation();
    const [dateFrom, setDateFrom] = useState(filters?.date_from || '');
    const [dateTo, setDateTo] = useState(filters?.date_to || '');
    const list = Array.isArray(rows) ? rows : [];
    const summaryList = Array.isArray(summaries) ? summaries : [];
    const exportQuery = buildExportQuery({ date_from: dateFrom, date_to: dateTo, employee_id: filters?.employee_id });

    return (
        <ErpLayout>
            <Head title={t('reports.payroll')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.payroll')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>
            <form onSubmit={(e) => { e.preventDefault(); router.get(route('reports.payroll'), { date_from: dateFrom, date_to: dateTo }, { preserveState: true }); }} className="mb-4 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
                <ShamsiDateInput label={t('fields.date_from')} value={dateFrom} onChange={setDateFrom} />
                <ShamsiDateInput label={t('fields.date_to')} value={dateTo} onChange={setDateTo} />
                <PrimaryButton type="submit">{t('actions.filter')}</PrimaryButton>
                <ReportExportButtons exportType="payroll" queryString={exportQuery} />
            </form>

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
                    <thead className="bg-gray-50"><tr><th className="px-4 py-3 text-left">{t('fields.date')}</th><th className="px-4 py-3 text-left">{t('fields.employee')}</th><th className="px-4 py-3 text-left">{t('fields.amount')}</th><th className="px-4 py-3 text-left">{t('fields.payment_type')}</th></tr></thead>
                    <tbody className="divide-y">
                        {list.map((r) => (
                            <tr key={r.id}><td className="px-4 py-3">{r.payment_date_shamsi || r.payment_date}</td><td className="px-4 py-3">{r.employee?.name}</td><td className="px-4 py-3">{formatCurrency(r.amount)}</td><td className="px-4 py-3">{r.payment_type}</td></tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
