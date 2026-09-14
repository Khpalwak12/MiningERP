import ReportFilterForm from '@/Components/Erp/ReportFilterForm';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { resourceItems } from '@/utils/resource';
import { Head, Link } from '@inertiajs/react';

export default function Payments({ rows, summary, filters, customers, employees, expenseCategories, selectedCustomer, selectedEmployee }) {
    const { t } = useTranslation();
    const list = resourceItems(rows);

    return (
        <ErpLayout>
            <Head title={t('reports.payments')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.payments')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>

            <ReportFilterForm
                reportType="payments"
                routeName="reports.payments"
                filters={filters}
                exportType="payments"
                lookups={{ customers, employees, expenseCategories, selectedCustomer, selectedEmployee }}
            />

            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.customer')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.receipt_number')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.received_by')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.amount')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && <tr><td colSpan={5} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>}
                        {list.map((r) => (
                            <tr key={r.id}>
                                <td className="px-4 py-3">{r.payment_date_shamsi}</td>
                                <td className="px-4 py-3">{r.customer?.name}</td>
                                <td className="px-4 py-3">{r.receipt_number || '—'}</td>
                                <td className="px-4 py-3">{r.received_by}</td>
                                <td className="px-4 py-3">{formatCurrency(r.amount)}</td>
                            </tr>
                        ))}
                        {list.length > 0 && summary && (
                            <tr className="bg-gray-50 font-semibold">
                                <td className="px-4 py-3">{t('reports.totals')}</td>
                                <td className="px-4 py-3">—</td>
                                <td className="px-4 py-3">—</td>
                                <td className="px-4 py-3">—</td>
                                <td className="px-4 py-3">{formatCurrency(summary.amount)}</td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
