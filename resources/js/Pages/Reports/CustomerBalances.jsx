import ReportFilterForm from '@/Components/Erp/ReportFilterForm';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { resourceItems } from '@/utils/resource';
import { Head, Link } from '@inertiajs/react';

export default function CustomerBalances({ customers, summary, filters }) {
    const { t } = useTranslation();
    const list = resourceItems(customers);

    return (
        <ErpLayout>
            <Head title={t('reports.customer_balances')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.customer_balances')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>

            <ReportFilterForm
                reportType="customer-balances"
                routeName="reports.customer-balances"
                filters={filters}
                exportType="customer-balances"
            />

            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.customer')}</th>
                            <th className="px-4 py-3 text-left">{t('reports.total_sales')}</th>
                            <th className="px-4 py-3 text-left">{t('reports.total_payments')}</th>
                            <th className="px-4 py-3 text-left">{t('reports.outstanding_balance')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && <tr><td colSpan={4} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>}
                        {list.map((c) => (
                            <tr key={c.id}>
                                <td className="px-4 py-3">{c.name}</td>
                                <td className="px-4 py-3">{formatCurrency(c.total_sales)}</td>
                                <td className="px-4 py-3">{formatCurrency(c.total_payments)}</td>
                                <td className="px-4 py-3 font-medium text-amber-700">{formatCurrency(c.outstanding_balance)}</td>
                            </tr>
                        ))}
                        {list.length > 0 && summary && (
                            <tr className="bg-gray-50 font-semibold">
                                <td className="px-4 py-3">{t('reports.totals')}</td>
                                <td className="px-4 py-3">{formatCurrency(summary.total_sales)}</td>
                                <td className="px-4 py-3">{formatCurrency(summary.total_payments)}</td>
                                <td className="px-4 py-3 text-amber-700">{formatCurrency(summary.outstanding_balance)}</td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
