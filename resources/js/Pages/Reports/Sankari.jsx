import ReportFilterForm from '@/Components/Erp/ReportFilterForm';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { resourceItems } from '@/utils/resource';
import { Head, Link } from '@inertiajs/react';

export default function Sankari({ rows, summary, filters }) {
    const { t } = useTranslation();
    const list = resourceItems(rows);

    return (
        <ErpLayout>
            <Head title={t('reports.sankari')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.sankari')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>

            <ReportFilterForm
                reportType="sankari"
                routeName="reports.sankari"
                filters={filters}
                exportType="sankari"
            />

            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.truck_count')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.price_per_truck')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.subtotal')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.discount')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.total_amount')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.notes')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && (
                            <tr><td colSpan={7} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {list.map((row) => (
                            <tr key={row.id}>
                                <td className="px-4 py-3">{row.sale_date_shamsi}</td>
                                <td className="px-4 py-3">{row.truck_count}</td>
                                <td className="px-4 py-3">{formatCurrency(row.price_per_truck)}</td>
                                <td className="px-4 py-3">{formatCurrency(row.subtotal)}</td>
                                <td className="px-4 py-3">{formatCurrency(row.discount)}</td>
                                <td className="px-4 py-3 font-medium">{formatCurrency(row.total_amount)}</td>
                                <td className="px-4 py-3">{row.notes || '—'}</td>
                            </tr>
                        ))}
                        {list.length > 0 && summary && (
                            <tr className="bg-gray-50 font-semibold">
                                <td className="px-4 py-3">{t('reports.totals')}</td>
                                <td className="px-4 py-3">{summary.truck_count}</td>
                                <td className="px-4 py-3">—</td>
                                <td className="px-4 py-3">{formatCurrency(summary.subtotal)}</td>
                                <td className="px-4 py-3">{formatCurrency(summary.discount)}</td>
                                <td className="px-4 py-3">{formatCurrency(summary.total_amount)}</td>
                                <td className="px-4 py-3">—</td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
