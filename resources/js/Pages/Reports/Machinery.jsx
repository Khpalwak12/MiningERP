import ReportFilterForm from '@/Components/Erp/ReportFilterForm';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import StatCard from '@/Components/Erp/StatCard';
import useTranslation from '@/hooks/useTranslation';
import { formatMoney } from '@/utils/format';
import { resourceItems } from '@/utils/resource';
import { Head, Link } from '@inertiajs/react';

export default function Machinery({ rows, filters, totals }) {
    const { t } = useTranslation();
    const list = resourceItems(rows);

    return (
        <ErpLayout>
            <Head title={t('reports.machinery')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.machinery')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>

            <ReportFilterForm
                reportType="machinery"
                routeName="reports.machinery"
                filters={filters}
                exportType="machinery"
                showPersonalCurrency
            />

            <div className="mb-4 grid gap-4 sm:grid-cols-2">
                <StatCard title={t('machinery.total_afn')} value={formatMoney(totals?.AFN)} color="amber" />
                <StatCard title={t('machinery.total_usd')} value={formatMoney(totals?.USD, 'USD')} color="indigo" />
            </div>

            <p className="mb-4 text-sm text-gray-600">{t('reports.total_records')}: {list.length}</p>

            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.name')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.bill_number')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.currency')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.amount')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.description')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && (
                            <tr><td colSpan={6} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {list.map((row) => (
                            <tr key={row.id}>
                                <td className="px-4 py-3">{row.purchase_date_shamsi}</td>
                                <td className="px-4 py-3 font-medium">{row.item_name}</td>
                                <td className="px-4 py-3">{row.bill_number || '—'}</td>
                                <td className="px-4 py-3">{t(`currencies.${row.currency}`)}</td>
                                <td className="px-4 py-3">{formatMoney(row.amount, row.currency)}</td>
                                <td className="px-4 py-3">{row.description || '—'}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
